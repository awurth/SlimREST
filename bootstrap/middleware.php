<?php

$app->add(new App\Middleware\CORSMiddleware($config['settings']['cors']));
