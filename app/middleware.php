<?php

use App\Middleware\Authorization;
use App\Middleware\Cors;

$container['auth.middleware'] = function ($container) {
    return new Authorization($container['oauth'], $container['sentinel']);
};

$app->add(new Cors($container['settings']['cors']));
