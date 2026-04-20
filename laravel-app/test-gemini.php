<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\GeminiService;

echo "Testing Gemini API...\n\n";

try {
    $gemini = new GeminiService();
    
    echo "1. Testing simple generation...\n";
    $result = $gemini->generateContent('Say hello in one word');
    echo "Response: " . $result['text'] . "\n\n";
    
    echo "2. Testing NLP command processing...\n";
    $nlpResult = $gemini->processCommand('Create a task to test the API');
    echo "Intent: " . $nlpResult['intent'] . "\n";
    echo "Confidence: " . $nlpResult['confidence'] . "\n";
    echo "Response: " . ($nlpResult['natural_response'] ?? $nlpResult['error'] ?? 'No response') . "\n";
    if (!empty($nlpResult['entities'])) {
        print_r($nlpResult['entities']);
    }
    if (!empty($nlpResult['error'])) {
        echo "Error: " . $nlpResult['error'] . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
