<?php

$container['AuthController'] = function ($container) {
    return new App\Controller\AuthController($container);
};
