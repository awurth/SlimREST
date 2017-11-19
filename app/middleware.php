<?php

use App\Security\Middleware\Authorization;
use App\Security\Middleware\Cors;

$container['auth.middleware'] = function ($container) {
    return new Authorization($container['oauth'], $container['sentinel']);
};

$app->add(new Cors($container['config']['cors']));
