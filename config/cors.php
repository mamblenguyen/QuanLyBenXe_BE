<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    // ⚠️ CHỈ ĐỊNH rõ domain của frontend
    'allowed_origins' => ['http://54.151.156.187', 'http://localhost:3000'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // ⚠️ BẮT BUỘC để dùng withCredentials (cookie)
    'supports_credentials' => true,

];
