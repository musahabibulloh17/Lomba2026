<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Gemini AI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google Gemini AI API integration
    |
    */

    'api_key' => env('GEMINI_API_KEY'),
    
    'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    
    'max_tokens' => env('GEMINI_MAX_TOKENS', 1000),
    
    'temperature' => env('GEMINI_TEMPERATURE', 0.3),
    
    'api_url' => 'https://generativelanguage.googleapis.com/v1beta/models/',
];
