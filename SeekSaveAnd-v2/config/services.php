<?php

return [

    'postmark' => ['key' => env('POSTMARK_API_KEY')],

    'resend' => ['key' => env('RESEND_API_KEY')],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Microsoft school account sign-in (Microsoft Entra ID / Azure AD).
    // Socialite::driver('microsoft') reads the "microsoft" key.
    'microsoft' => [
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'redirect' => env('AZURE_REDIRECT_URI', env('APP_URL', 'http://localhost:8000') . '/auth/microsoft/callback'),
        'tenant' => env('AZURE_TENANT_ID', 'common'),
    ],

    // Keep the azure alias for compatibility with existing local configuration.
    'azure' => [
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'redirect' => env('AZURE_REDIRECT_URI', env('APP_URL', 'http://localhost:8000') . '/auth/microsoft/callback'),
        'tenant' => env('AZURE_TENANT_ID', 'common'),
    ],

    // Supabase Storage (item photos)
    'supabase' => [
        'url' => env('SUPABASE_URL'),
        'key' => env('SUPABASE_SERVICE_KEY'),
        'bucket' => env('SUPABASE_BUCKET', 'item-photos'),
    ],

];
