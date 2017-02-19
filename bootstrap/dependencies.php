<?php

use Symfony\Component\Yaml\Yaml;
use Illuminate\Database\Capsule\Manager;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use App\Service\JWTManager;
use Awurth\Slim\Validation\Validator;

$container = $app->getContainer();

$parameters = Yaml::parse(file_get_contents(__DIR__ . '/parameters.yml'))['parameters'];

$capsule = new Manager();
$capsule->addConnection($parameters['eloquent']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['sentinel'] = function () {
    $sentinel = new Sentinel(new SentinelBootstrapper(__DIR__ . '/sentinel.php'));

    return $sentinel->getSentinel();
};

$container['jwt'] = function () use ($parameters, $config) {
    return new JWTManager($parameters['security']['secret'], $config['jwt']);
};

$container['validator'] = function () {
    return new Validator();
};
