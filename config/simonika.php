<?php

return [
    'demo_seed' => [
        'enabled' => env('SIMONIKA_DEMO_SEED', false),
        'email' => env('SIMONIKA_DEMO_ADMIN_EMAIL', 'admin@example.test'),
        'password' => env('SIMONIKA_DEMO_ADMIN_PASSWORD'),
    ],
];
