<?php

use Illuminate\Database\Capsule\Manager;

$tables = [
    'sentinel' => [
        'activations',
        'persistences',
        'reminders',
        'role_users',
        'throttle',
        'roles',
        'access_token',
        'refresh_token',
        'user',
    ],
    'oauth' => [
        'oauth_clients',
        'oauth_access_tokens',
        'oauth_authorization_codes',
        'oauth_refresh_tokens',
        'oauth_scopes',
        'oauth_jwt'
    ],
    'app' => []
];

Manager::schema()->disableForeignKeyConstraints();
foreach ($tables as $section => $sectionTables) {
    foreach ($sectionTables as $table) {
        Manager::schema()->dropIfExists($table);
    }
}
