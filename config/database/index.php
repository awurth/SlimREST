<?php

Illuminate\Database\Capsule\Manager::schema()->defaultStringLength(191);

// Drop all tables
require __DIR__.'/drop.php';

// Create tables
require __DIR__.'/sentinel.php';
require __DIR__.'/oauth.php';
require __DIR__.'/app.php';
