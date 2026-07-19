<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

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

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-5.6-luna'),
    ],

    // Envío del boletín nacional por correo (lo dispara n8n).
    'bulletin_dispatch' => [
        // Token compartido — evita que cualquiera dispare el envío masivo.
        'token' => env('BULLETIN_DISPATCH_TOKEN'),
        // Mailer a usar: 'smtp' (una cuenta) o 'roundrobin' (varias, con failover).
        'mailer' => env('BULLETIN_DISPATCH_MAILER', 'smtp'),
        // Hora de INICIO (Colombia). Nunca se envía antes de esta hora. En días
        // normales se envía UNA vez al día a partir de aquí; en fechas especiales
        // se envía cada N horas desde esta hora.
        'daily_hour' => (int) env('BULLETIN_DISPATCH_DAILY_HOUR', 8),
        // En fechas especiales: cada cuántas horas se envía (default 4). El
        // workflow corre cada 2h; el backend solo envía cuando toca el intervalo.
        'special_interval_hours' => (int) env('BULLETIN_DISPATCH_SPECIAL_INTERVAL_HOURS', 4),
        'timezone' => env('BULLETIN_DISPATCH_TZ', 'America/Bogota'),
    ],
];
