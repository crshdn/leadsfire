<?php
/**
 * LeadsFire Click Tracker - Application Configuration
 * 
 * This file contains the main application configuration.
 * Values are loaded from .env file via the Config class.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'LeadsFire Click Tracker'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    | Options: production, development, testing
    */
    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    */
    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    */
    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    */
    'timezone' => env('APP_TIMEZONE', 'America/New_York'),

    /*
    |--------------------------------------------------------------------------
    | Date/Time Format
    |--------------------------------------------------------------------------
    */
    'date_format' => 'm/d/Y',
    'time_format' => 'g:i A',
    'datetime_format' => 'm/d/Y g:i A',

    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    */
    'session' => [
        'lifetime' => env('SESSION_LIFETIME', 86400),
        'name' => env('SESSION_NAME', 'leadsfire_session'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */
    'security' => [
        'allowed_ips' => array_filter(array_map('trim', explode(',', env('ALLOWED_IPS', '')))),
        'rate_limit' => [
            'login_attempts' => 5,
            'login_window' => 300, // 5 minutes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'level' => env('LOG_LEVEL', 'verbose'),
        'channel' => env('LOG_CHANNEL', 'daily'),
        'path' => __DIR__ . '/../storage/logs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking Configuration
    |--------------------------------------------------------------------------
    */
    'tracking' => [
        'cookie_timeout' => env('COOKIE_TIMEOUT', 2592000),
        'cookie_samesite' => env('COOKIE_SAMESITE', 'None'),
        'cookie_secure' => env('COOKIE_SECURE', true),
        'dedup_seconds' => env('DEDUP_SECONDS', 0),
        'attribution_days' => env('ATTRIBUTION_DAYS', 30),
        'ignore_prefetch' => env('IGNORE_PREFETCH', true),
        'log_direct_traffic' => env('LOG_DIRECT_TRAFFIC', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | GeoIP Configuration
    |--------------------------------------------------------------------------
    */
    'geoip' => [
        'provider' => env('GEOIP_PROVIDER', 'ip-api'),
        'maxmind' => [
            'license_key' => env('MAXMIND_LICENSE_KEY', ''),
            'database_path' => env('MAXMIND_DATABASE_PATH', 'storage/geoip/GeoLite2-City.mmdb'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    */
    'paths' => [
        'storage' => __DIR__ . '/../storage',
        'logs' => __DIR__ . '/../storage/logs',
        'cache' => __DIR__ . '/../storage/cache',
        'sessions' => __DIR__ . '/../storage/sessions',
        'uploads' => __DIR__ . '/../storage/uploads',
        'views' => __DIR__ . '/../src/Views',
    ],

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    */
    'version' => '1.0.0',
];

