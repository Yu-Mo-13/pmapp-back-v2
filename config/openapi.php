<?php

return [
    'info' => [
        'title' => env('OPENAPI_TITLE', env('APP_NAME', 'Laravel') . ' API'),
        'version' => env('OPENAPI_VERSION', '1.0.0'),
        'description' => env('OPENAPI_DESCRIPTION', 'Generated OpenAPI specification for the PMAPP API.'),
    ],
    'server_url' => env('OPENAPI_SERVER_URL', env('APP_URL', 'http://localhost')),
];
