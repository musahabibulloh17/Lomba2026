<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google APIs Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google Calendar and Gmail API integration
    |
    */

    'client_id' => env('GOOGLE_CLIENT_ID'),
    
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    
    'redirect_uri' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
    
    'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),
    
    'scopes' => [
        'https://www.googleapis.com/auth/calendar',
        'https://www.googleapis.com/auth/gmail.send',
        'https://www.googleapis.com/auth/gmail.readonly',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
    ],
    
    'calendar' => [
        'timezone' => env('GOOGLE_CALENDAR_TIMEZONE', 'Asia/Jakarta'),
    ],
];
