<?php

$controllers = [
    'app.controller'          => 'App\Controller\AppController',
    'token.controller'        => 'App\Controller\TokenController',
    'registration.controller' => 'App\Controller\RegistrationController'
];

foreach ($controllers as $key => $class) {
    $container[$key] = function ($container) use ($class) {
        return new $class($container);
    };
}
