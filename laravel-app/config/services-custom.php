<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Services Configuration
    |--------------------------------------------------------------------------
    |
    | Additional service configuration for the application
    |
    */

    'rate_limiting' => [
        'window_ms' => env('RATE_LIMIT_WINDOW_MS', 900000),
        'max_requests' => env('RATE_LIMIT_MAX_REQUESTS', 100),
    ],
    
    'reminders' => [
        'enabled' => env('ENABLE_REMINDERS', true),
        'check_interval' => env('REMINDER_CHECK_INTERVAL', '*/5 * * * *'),
    ],
    
    'cors' => [
        'origin' => env('CORS_ORIGIN', 'http://localhost:3001'),
    ],
    
    'api' => [
        'version' => env('API_VERSION', 'v1'),
    ],
];
