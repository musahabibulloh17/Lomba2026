<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $token;
    protected $apiUrl;
    protected $enabled;

    public function __construct()
    {
        $this->token = config('fonnte.token');
        $this->apiUrl = config('fonnte.api_url');
        $this->enabled = config('fonnte.enabled', true);
    }

    /**
     * Send WhatsApp message via Fonnte
     * 
     * @param string $phoneNumber Phone number with country code (e.g., 628123456789)
     * @param string $message Message content
     * @return array Response from API
     */
    public function sendMessage(string $phoneNumber, string $message): array
    {
        if (!$this->enabled) {
            Log::info('Fonnte is disabled');
            return [
                'success' => false,
                'message' => 'WhatsApp service is disabled'
            ];
        }

        if (empty($this->token)) {
            Log::error('Fonnte token is not configured');
            return [
                'success' => false,
                'message' => 'WhatsApp token not configured'
            ];
        }

        // Clean phone number (remove spaces, dashes, etc.)
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Ensure phone number starts with country code
        if (!str_starts_with($phoneNumber, '62') && !str_starts_with($phoneNumber, '+62')) {
            // Assume Indonesian number, add 62
            $phoneNumber = '62' . ltrim($phoneNumber, '0');
        }

        Log::info('Sending WhatsApp message via Fonnte', [
            'to' => $phoneNumber,
            'message_length' => strlen($message)
        ]);

        try {
            $response = Http::timeout(config('fonnte.timeout', 30))
                ->withHeaders([
                    'Authorization' => $this->token,
                ])
                ->post($this->apiUrl, [
                    'target' => $phoneNumber,
                    'message' => $message,
                    'countryCode' => '62', // Indonesia
                ]);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $phoneNumber,
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => $responseData
                ];
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'to' => $phoneNumber,
                    'status' => $response->status(),
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send message',
                    'error' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending WhatsApp message', [
                'to' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send task reminder via WhatsApp
     * 
     * @param string $phoneNumber
     * @param object $task
     * @return array
     */
    public function sendTaskReminder(string $phoneNumber, $task): array
    {
        $dueDate = \Carbon\Carbon::parse($task->due_date);
        $now = \Carbon\Carbon::now();
        $minutesLeft = $now->diffInMinutes($dueDate, false);

        $message = "🔔 *Task Reminder - FlowSpec AI*\n\n";
        $message .= "📋 *Task:* {$task->title}\n";
        
        if ($task->description) {
            $message .= "📝 *Description:* {$task->description}\n";
        }
        
        $message .= "⏰ *Due:* {$dueDate->format('d M Y, H:i')}\n";
        
        if ($minutesLeft > 0) {
            $message .= "⚠️ *Time Left:* {$minutesLeft} minutes\n";
        } else {
            $message .= "🚨 *Status:* OVERDUE!\n";
        }
        
        $priorityEmoji = $task->priority === 'high' ? '🔴' : ($task->priority === 'medium' ? '🟡' : '🟢');
        $message .= "{$priorityEmoji} *Priority:* " . ucfirst($task->priority) . "\n\n";
        $message .= "Please complete this task soon! 💪";

        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Send meeting reminder via WhatsApp
     * 
     * @param string $phoneNumber
     * @param object $meeting
     * @return array
     */
    public function sendMeetingReminder(string $phoneNumber, $meeting): array
    {
        $startTime = \Carbon\Carbon::parse($meeting->start_time);
        $now = \Carbon\Carbon::now();
        $minutesLeft = $now->diffInMinutes($startTime, false);

        $message = "🔔 *Meeting Reminder - FlowSpec AI*\n\n";
        $message .= "📅 *Meeting:* {$meeting->title}\n";
        
        if ($meeting->description) {
            $message .= "📝 *Description:* {$meeting->description}\n";
        }
        
        $message .= "⏰ *Start:* {$startTime->format('d M Y, H:i')}\n";
        
        if ($minutesLeft > 0) {
            $message .= "⚠️ *Starts in:* {$minutesLeft} minutes\n";
        } else {
            $message .= "🚨 *Status:* STARTING NOW!\n";
        }
        
        if (!empty($meeting->meeting_link)) {
            $message .= "🔗 *Link:* {$meeting->meeting_link}\n";
        }
        
        $message .= "\nDon't be late! 🏃‍♂️";

        return $this->sendMessage($phoneNumber, $message);
    }
}
