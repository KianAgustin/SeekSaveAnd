<?php

return [
    'campuses' => ['MCC Main', 'MCC Dapdap'],
    'programs' => ['BSIT', 'BSCS', 'BSIS', 'BSBA', 'BSED', 'Other'],
    'years' => ['1st Year', '2nd Year', '3rd Year', '4th Year'],
    'sections' => ['A', 'B', 'C', 'D'],
    'categories' => ['Electronics', 'Keys', 'Documents', 'Clothing', 'Pets', 'Personal Item', 'Other'],
    'locations' => [
        'Cisco Laboratory', 'Campus Canteen', 'Library', 'Boys CR', 'Girls CR',
        'Room 105', 'Room 201', 'Student Center', 'Computer Lab', 'Parking Lot B',
        'Gymnasium', 'Admin Building', 'Auditorium',
    ],

    // Only emails ending in @this-domain may sign in (leave empty to allow the whole tenant).
    'allowed_domain' => env('MICROSOFT_ALLOWED_DOMAIN', ''),

    // These Microsoft emails become admins automatically when they sign in.
    'admin_emails' => array_values(array_filter(array_map(
        fn ($e) => strtolower(trim($e)),
        explode(',', (string) env('ADMIN_EMAILS', ''))
    ))),

    // Local testing only: shows "sign in as" buttons for seeded users (never works outside APP_ENV=local).
    'dev_login' => (bool) env('SEEKSAVE_DEV_LOGIN', false),

    // Send emails (through your Microsoft 365 SMTP) for new claims / first messages.
    'email_notifications' => (bool) env('SEEKSAVE_EMAIL_NOTIFICATIONS', false),
];
