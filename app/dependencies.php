<?php

use App\Security\Jwt\Manager as JwtManager;
use Awurth\SlimValidation\Validator;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Illuminate\Database\Capsule\Manager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Symfony\Component\Yaml\Yaml;

$container = $app->getContainer();

$parameters = Yaml::parse(file_get_contents(__DIR__ . '/config/parameters.yml'))['parameters'];

$capsule = new Manager();
$capsule->addConnection($parameters);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['sentinel'] = function () {
    $sentinel = new Sentinel(new SentinelBootstrapper(__DIR__ . '/config/sentinel.php'));

    return $sentinel->getSentinel();
};

$container['jwt'] = function () use ($parameters, $container) {
    return new JwtManager($parameters['secret'], $container['config']['jwt']);
};

$container['validator'] = function () {
    return new Validator(false);
};

$container['monolog'] = function ($container) {
    $config = $container['config']['monolog'];

    $logger = new Logger($config['name']);
    $logger->pushProcessor(new UidProcessor());
    $logger->pushHandler(new StreamHandler($config['path'], $config['level']));

    return $logger;
};
