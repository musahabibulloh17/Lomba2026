<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected $apiKey;
    protected $model;
    protected $maxRetries = 3;
    protected $baseDelay = 2000; // milliseconds - increased from 1000

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        $this->model = config('gemini.model', 'gemini-2.5-flash');
    }

    /**
     * Call Gemini API with retry logic for rate limiting
     */
    public function generateContent(string $prompt, int $retryCount = 0): array
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
            
            Log::info("Calling Gemini API (attempt " . ($retryCount + 1) . ")");
            
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => config('gemini.max_tokens', 1000),
                    ]
                ]);

            Log::info("Gemini API response status: " . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info("Gemini API response received", ['has_candidates' => isset($data['candidates'])]);
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return [
                        'success' => true,
                        'text' => $data['candidates'][0]['content']['parts'][0]['text'],
                        'raw' => $data,
                    ];
                }
                
                Log::error('Invalid response format from Gemini API', ['response' => $data]);
                throw new Exception('Invalid response format from Gemini API');
            }

            // Handle rate limiting
            if ($response->status() === 429) {
                if ($retryCount < $this->maxRetries) {
                    $delayMs = $this->baseDelay * pow(2, $retryCount);
                    Log::warning("Gemini API rate limit hit, retrying in {$delayMs}ms... (attempt " . ($retryCount + 1) . "/{$this->maxRetries})");
                    
                    usleep($delayMs * 1000); // Convert to microseconds
                    return $this->generateContent($prompt, $retryCount + 1);
                }
                
                throw new Exception('Gemini API quota exceeded. Please try again in a few minutes.');
            }

            Log::error('Gemini API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            throw new Exception('Gemini API request failed: ' . $response->body());
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini API Connection Error: ' . $e->getMessage());
            throw new Exception('Failed to connect to Gemini API. Please check your internet connection.');
        } catch (Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process natural language command and extract intent
     */
    public function processCommand(string $command): array
    {
        $prompt = $this->buildNLPPrompt($command);
        
        try {
            $result = $this->generateContent($prompt);
            
            if ($result['success']) {
                return $this->parseNLPResponse($result['text']);
            }
            
            return [
                'intent' => 'unknown',
                'entities' => [],
                'confidence' => 0,
                'natural_response' => 'I could not process your command. Please try again.',
                'error' => 'Failed to process command'
            ];
            
        } catch (Exception $e) {
            // Check if it's a quota error
            if (str_contains($e->getMessage(), 'quota') || str_contains($e->getMessage(), 'rate limit')) {
                return [
                    'intent' => 'error',
                    'entities' => [],
                    'confidence' => 0,
                    'natural_response' => '⏳ AI is temporarily unavailable due to rate limiting. Please try again in a few minutes. You can still use the Tasks and Meetings pages directly.',
                    'error' => $e->getMessage()
                ];
            }
            
            return [
                'intent' => 'error',
                'entities' => [],
                'confidence' => 0,
                'natural_response' => 'Sorry, I encountered an error: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build NLP prompt for intent detection
     */
    protected function buildNLPPrompt(string $command): string
    {
        $currentDate = now()->format('Y-m-d');
        $currentDateTime = now()->format('Y-m-d H:i:s');
        $tomorrow = now()->addDay()->format('Y-m-d');
        
        return <<<PROMPT
You are an AI assistant for a workflow management system. 

CURRENT DATE AND TIME CONTEXT:
- Today's date: {$currentDate}
- Current datetime: {$currentDateTime}
- Tomorrow's date: {$tomorrow}
- Day of week: {$this->getDayOfWeek()}

IMPORTANT: When parsing dates and times:
- "tomorrow" = {$tomorrow}
- "today" = {$currentDate}
- "next week" = add 7 days from today
- Always use YYYY-MM-DD format for dates
- Always use YYYY-MM-DD HH:MM:SS format for datetimes
- Use 24-hour time format

Analyze the following user command and extract:
1. Intent (what the user wants to do)
2. Entities (relevant data like dates, priorities, titles, etc.)

Available intents:
- create_task: Create a new task
- list_tasks: List/show tasks
- update_task: Update an existing task
- delete_task: Delete a task
- create_meeting: Schedule a new meeting
- list_meetings: List/show meetings
- update_meeting: Update a meeting
- delete_meeting: Cancel a meeting
- general_query: General questions or chat

User command: "{$command}"

Respond in JSON format:
{
  "intent": "intent_name",
  "entities": {
    "title": "extracted title if any",
    "description": "extracted description if any",
    "due_date": "extracted date in YYYY-MM-DD format if any",
    "priority": "low|medium|high if mentioned",
    "status": "pending|in_progress|completed if mentioned",
    "start_time": "extracted datetime in YYYY-MM-DD HH:MM:SS format if any",
    "end_time": "extracted datetime in YYYY-MM-DD HH:MM:SS format if any",
    "attendees": ["email1@example.com", "email2@example.com"] if mentioned
  },
  "confidence": 0.95,
  "natural_response": "A friendly response to the user"
}

Only return valid JSON, no additional text.
PROMPT;
    }

    /**
     * Get current day of week
     */
    protected function getDayOfWeek(): string
    {
        return now()->format('l, F j, Y');
    }

    /**
     * Parse NLP response from Gemini
     */
    protected function parseNLPResponse(string $response): array
    {
        // Try to extract JSON from response
        $response = trim($response);
        
        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*$/', '', $response);
        $response = trim($response);
        
        try {
            $data = json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                return [
                    'intent' => $data['intent'] ?? 'unknown',
                    'entities' => $data['entities'] ?? [],
                    'confidence' => $data['confidence'] ?? 0.5,
                    'natural_response' => $data['natural_response'] ?? 'I understood your command.',
                ];
            }
        } catch (Exception $e) {
            Log::error('Failed to parse Gemini response: ' . $e->getMessage());
        }
        
        // Fallback
        return [
            'intent' => 'general_query',
            'entities' => [],
            'confidence' => 0.3,
            'natural_response' => $response,
        ];
    }

    /**
     * Generate a conversational response
     */
    public function chat(string $message, array $context = []): string
    {
        $contextStr = '';
        if (!empty($context)) {
            $contextStr = "\n\nContext:\n" . json_encode($context, JSON_PRETTY_PRINT);
        }

        $prompt = <<<PROMPT
You are a helpful AI assistant for a workflow management system called FlowSpec AI.

User message: "{$message}"
{$contextStr}

Provide a helpful, friendly, and concise response. If the user is asking about tasks or meetings, 
provide relevant information based on the context if available.

Keep your response under 150 words.
PROMPT;

        try {
            $result = $this->generateContent($prompt);
            return $result['success'] ? $result['text'] : 'Sorry, I encountered an error processing your request.';
        } catch (Exception $e) {
            Log::error('Gemini chat error: ' . $e->getMessage());
            return 'Sorry, I am temporarily unavailable. Please try again later.';
        }
    }
}
