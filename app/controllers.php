<?php

$container['AuthController'] = function ($container) {
    return new App\Security\Controller\AuthController($container);
};
