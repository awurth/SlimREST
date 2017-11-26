<?php

$controllers = [
    'app.controller'                   => 'App\Controller\AppController',
    'security.token.controller'        => 'App\Controller\TokenController',
    'security.registration.controller' => 'App\Controller\RegistrationController'
];

foreach ($controllers as $key => $class) {
    $container[$key] = function ($container) use ($class) {
        return new $class($container);
    };
}
