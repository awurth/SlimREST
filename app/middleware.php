<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\JwtMiddleware;

$container['auth.middleware'] = function ($container) {
    return function ($role = null) use ($container) {
        return new AuthMiddleware($container['sentinel'], $role);
    };
};

$app->add(new JwtMiddleware($container['jwt']));
$app->add(new CorsMiddleware($container['config']['cors']));
