<?php

use App\Security\Middleware\CorsMiddleware;

$app->add(new CorsMiddleware($container['config']['cors']));
