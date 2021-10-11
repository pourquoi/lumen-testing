<?php

return [
    'default' => 'local',

    'connections' => [

        'awss3' => [
            'driver'          => 'awss3',
            'key'             => 'your-key',
            'secret'          => 'your-secret',
            'bucket'          => 'your-bucket',
            'region'          => 'your-region',
            'version'         => 'latest',
            // 'bucket_endpoint' => false,
            // 'calculate_md5'   => true,
            // 'scheme'          => 'https',
            // 'endpoint'        => 'your-url',
            // 'prefix'          => 'your-prefix',
            // 'visibility'      => 'public',
            // 'pirate'          => false,
            // 'eventable'       => true,
            // 'cache'           => 'foo'
        ],

        'local' => [
            'driver'     => 'local',
            'path'       => base_path('public/uploads'),
            // 'visibility' => 'public',
            // 'pirate'     => false,
            // 'eventable'  => true,
            // 'cache'      => 'foo'
        ],

        'null' => [
            'driver'    => 'null',
            // 'eventable' => true,
            // 'cache'     => 'foo'
        ],

    ]
];



