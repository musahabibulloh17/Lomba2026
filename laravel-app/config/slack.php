<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Slack Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Slack API integration
    |
    */

    'bot_token' => env('SLACK_BOT_TOKEN'),
    
    'signing_secret' => env('SLACK_SIGNING_SECRET'),
    
    'default_channel' => env('SLACK_DEFAULT_CHANNEL', '#general'),
    
    'webhook_url' => env('SLACK_WEBHOOK_URL'),
    
    'api_url' => 'https://slack.com/api/',
];
