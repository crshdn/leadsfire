<?php
/**
 * LeadsFire Click Tracker - Mail Configuration
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    | Options: mailgun, smtp, sendgrid, ses, log
    */
    'driver' => env('MAIL_DRIVER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    */
    'mailers' => [
        'mailgun' => [
            'domain' => env('MAILGUN_DOMAIN', ''),
            'api_key' => env('MAILGUN_API_KEY', ''),
            'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        ],

        'smtp' => [
            'host' => env('SMTP_HOST', ''),
            'port' => env('SMTP_PORT', 587),
            'username' => env('SMTP_USERNAME', ''),
            'password' => env('SMTP_PASSWORD', ''),
            'encryption' => env('SMTP_ENCRYPTION', 'tls'),
        ],

        'sendgrid' => [
            'api_key' => env('SENDGRID_API_KEY', ''),
        ],

        'ses' => [
            'key' => env('AWS_ACCESS_KEY_ID', ''),
            'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],

        'log' => [
            'path' => __DIR__ . '/../storage/logs/mail.log',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
        'name' => env('MAIL_FROM_NAME', 'LeadsFire Click Tracker'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Recipient
    |--------------------------------------------------------------------------
    */
    'to' => [
        'address' => env('MAIL_TO_ADDRESS', ''),
    ],
];

