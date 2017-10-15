<?php

$app->add(new App\Middleware\JWTMiddleware($container['jwt']));
$app->add(new App\Middleware\CORSMiddleware($container['cors']));
