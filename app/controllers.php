<?php

$controllers = [
    'core.controller' => 'App\Core\Controller\CoreController',
    'security.token.controller' => 'App\Security\Controller\TokenController',
    'security.registration.controller' => 'App\Security\Controller\RegistrationController'
];

foreach ($controllers as $key => $class) {
    $container[$key] = function ($container) use ($class) {
        return new $class($container);
    };
}
