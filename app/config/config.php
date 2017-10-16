<?php

use Monolog\Logger;

return [

    'rest' => [
        'prefix' => '/api', // All URLs generated with the RestRouter will be prefixed with this.
        'key'    => 'id', // Default key to use for single resources.
        /**
         * Default requirement for single resources key.
         * To disable it, use empty string ('') rather than null.
         */
        'requirement' => '[0-9]+',
        'crud' => [
            'get'               => true,
            'get_collection'    => true,
            'post'              => true,
            'put'               => true,
            'delete'            => true,
            'delete_collection' => false
        ]
    ],

    // The routes that should be generated by the CRUD method of the RestRouter.
    'cors' => [
        'origin'         => '*',
        'allow_headers'  => 'X-Requested-With, Content-Type, Accept, Origin, Authorization',
        'expose_headers' => 'Location, Content-Range',
        'methods'        => 'GET, POST, PUT, PATCH, DELETE',
        'max_age'        => 3600
    ],

    'jwt' => [
        'server_name'            => 'localhost', // The server name used to encrypt access tokens and refresh tokens.
        'access_token_lifetime'  => 3600, // seconds (1h)
        'refresh_token_lifetime' => 1209600 // seconds (14 days)
    ],

    'monolog' => [
        'name' => 'app',
        'path' => $container['root_dir'] . '/var/logs/' . $container['env'] . '.log',
        'level' => Logger::ERROR
    ]

];