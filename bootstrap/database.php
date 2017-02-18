<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager;
use Symfony\Component\Yaml\Yaml;

$parameters = Yaml::parse(file_get_contents(__DIR__ . '/parameters.yml'));

$capsule = new Manager();
$capsule->addConnection($parameters['parameters']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

require __DIR__ . '/database/auth.php';
