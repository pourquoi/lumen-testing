<?php

return [
    'default' => env('QUEUE_CONNECTION', 'local'),

    'connections' => [

        'local' => [
            'driver' => env('QUEUE_DRIVER', 'sync'),
            'host' => env('QUEUE_HOST', 'localhost'),
            'queue' => 'default',
            'retry_after' => 90,
        ],

    ]
];

