<?php

return [
    /**
     * Default API key used when no DB record exists.
     * It's safer to keep real keys in environment variables or a secrets manager.
     */
    'api_key' => env('SUPPORT_SERVICE_API_KEY', ''),

    /**
     * Default endpoint base URL used when no DB record exists.
     */
    'endpoint' => env('SUPPORT_SERVICE_ENDPOINT', ''),

    /**
     * Default headers to include on requests. You can set 'authorization' => 'bearer'
     * or 'x-api-key' depending on provider. The service will set the Authorization
     * header with the decrypted API key by default.
     */
    'headers' => [
        // 'Authorization' => 'Bearer',
        // 'X-API-KEY' => true,
    ],

    // HTTP client timeout in seconds
    'timeout' => 10,
];
