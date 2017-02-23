<?php

use Illuminate\Database\Capsule\Manager;

Manager::schema()->dropIfExists('activations');
Manager::schema()->dropIfExists('persistences');
Manager::schema()->dropIfExists('reminders');
Manager::schema()->dropIfExists('role_users');
Manager::schema()->dropIfExists('throttle');
Manager::schema()->dropIfExists('roles');
Manager::schema()->dropIfExists('access_token');
Manager::schema()->dropIfExists('refresh_token');

Manager::schema()->dropIfExists('user');
