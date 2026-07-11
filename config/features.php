<?php

return [
    'business_startup' => [
        'emails' => array_values(array_filter(array_map('trim', explode(',', (string) env('BUSINESS_STARTUP_EMAILS', ''))))),
    ],
];
