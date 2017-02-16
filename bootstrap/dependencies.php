<?php

use Symfony\Component\Yaml\Yaml;
use Illuminate\Database\Capsule\Manager;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Awurth\Slim\Validation\Validator;
use Slim\Handlers\Strategies\RequestResponseArgs;

$container = $app->getContainer();

$parameters = Yaml::parse(file_get_contents(__DIR__ . '/parameters.yml'));

$capsule = new Manager();
$capsule->addConnection($parameters['parameters']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['auth'] = function () {
    $sentinel = new Sentinel(
        new SentinelBootstrapper(__DIR__ . '/sentinel.php')
    );

    return $sentinel->getSentinel();
};

$container['validator'] = function () {
    return new Validator();
};

$container['foundHandler'] = function() {
    return new RequestResponseArgs();
};
