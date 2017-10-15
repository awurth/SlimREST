<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\CORSMiddleware;
use App\Middleware\JWTMiddleware;

$container['auth.middleware'] = function ($container) {
    return function ($role = null) use ($container) {
        return new AuthMiddleware($container['auth'], $role);
    };
};

$app->add(new JWTMiddleware($container['jwt']));
$app->add(new CORSMiddleware($container['cors']));
