<?php

return [
    'timeout' => (int) env('GATEWAY_TIMEOUT', 8),
    'upstreams' => [
        'auth' => env('AUTH_SERVICE_URL', 'http://auth-service:8000'),
        'asset' => env('ASSET_SERVICE_URL', 'http://asset-service:8000'),
        'history' => env('HISTORY_SERVICE_URL', 'http://history-service:8000'),
        'report' => env('REPORT_SERVICE_URL', 'http://report-service:8000'),
    ],
];