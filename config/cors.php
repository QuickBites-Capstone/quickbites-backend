<?php
return [

    'paths' => ['api/*'], // Allow CORS for API routes

    'allowed_methods' => ['*'], // Allow all methods

    'allowed_origins' => ['*'], // Allow all origins

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Allow all headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Allow credentials if needed
];
