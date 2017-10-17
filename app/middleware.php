<?php

use App\Security\Middleware\AuthMiddleware;
use App\Security\Middleware\CorsMiddleware;
use App\Security\Middleware\JwtMiddleware;

$container['auth.middleware'] = function ($container) {
    return function ($role = null) use ($container) {
        return new AuthMiddleware($container['jwt'], $role);
    };
};

$app->add(new JwtMiddleware($container['jwt']));
$app->add(new CorsMiddleware($container['config']['cors']));
