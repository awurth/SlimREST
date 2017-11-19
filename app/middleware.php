<?php

use App\Security\Middleware\Authorization;
use App\Security\Middleware\CorsMiddleware;

$container['auth.middleware'] = function ($container) {
    return new Authorization($container['oauth'], $container['sentinel']);
};

$app->add(new CorsMiddleware($container['config']['cors']));
