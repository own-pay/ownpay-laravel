<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | OwnPay API Key
    |--------------------------------------------------------------------------
    |
    | Your OwnPay API key. You can generate this from the OwnPay dashboard
    | under Developers > API Keys. The key should start with "op_" prefix.
    |
    */

    'api_key' => env('OWNPAY_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | OwnPay Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The webhook signing secret used to verify incoming webhook payloads.
    | This is configured in the OwnPay dashboard under Webhooks settings.
    |
    */

    'webhook_secret' => env('OWNPAY_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | OwnPay Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL of your OwnPay instance. This should be the full URL
    | including the protocol (https://).
    |
    */

    'base_url' => env('OWNPAY_BASE_URL', 'https://pay.ownpay.org'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The maximum number of seconds to wait for an API response. This applies
    | to both connection timeout and response timeout.
    |
    */

    'timeout' => env('OWNPAY_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Attempts
    |--------------------------------------------------------------------------
    |
    | The number of times to retry a failed API request. Retries are only
    | attempted for network errors and server errors (5xx), not for client
    | errors (4xx).
    |
    */

    'retry_attempts' => env('OWNPAY_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Retry Delay
    |--------------------------------------------------------------------------
    |
    | The base delay in milliseconds between retry attempts. The actual delay
    | uses exponential backoff with jitter: delay * 2^(attempt-1) + random jitter.
    |
    */

    'retry_delay' => env('OWNPAY_RETRY_DELAY', 100),

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    |
    | Whether to verify SSL certificates when making API requests. This should
    | always be true in production environments.
    |
    */

    'verify_ssl' => env('OWNPAY_VERIFY_SSL', true),

    /*
    |--------------------------------------------------------------------------
    | Log Channel
    |--------------------------------------------------------------------------
    |
    | The log channel to use for OwnPay SDK logging. Set to null to use the
    | default application log channel. Sensitive data like API keys are
    | automatically redacted from logs.
    |
    */

    'log_channel' => env('OWNPAY_LOG_CHANNEL'),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | The time-to-live in seconds for cached API responses. Set to 0 to
    | disable caching. Only GET requests are cached.
    |
    */

    'cache_ttl' => env('OWNPAY_CACHE_TTL', 0),

    /*
    |--------------------------------------------------------------------------
    | Idempotency Key Header
    |--------------------------------------------------------------------------
    |
    | The header name used for idempotency keys. OwnPay uses "Idempotency-Key"
    | by default. You should not need to change this.
    |
    */

    'idempotency_header' => 'Idempotency-Key',
];
