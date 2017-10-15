<?php

use Illuminate\Database\Capsule\Manager;

$tables = [
    'activations',
    'persistences',
    'reminders',
    'role_users',
    'throttle',
    'roles',
    'access_token',
    'refresh_token',
    'user',
];

foreach ($tables as $table) {
    Manager::schema()->dropIfExists($table);
}
