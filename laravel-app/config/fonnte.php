<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fonnte Configuration
    |--------------------------------------------------------------------------
    |
    | Fonnte API configuration for WhatsApp messaging
    |
    */

    'token' => env('FONNTE_TOKEN', ''),
    
    'enabled' => env('FONNTE_ENABLED', true),
    
    'api_url' => 'https://api.fonnte.com/send',
    
    'timeout' => 30,
];
