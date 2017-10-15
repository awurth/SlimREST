<?php

$app->add(new App\Middleware\JWTMiddleware($container));
$app->add(new App\Middleware\CORSMiddleware($container));
