<?php

$config = require __DIR__ . '/services.php';

// Used for generating links in API routes markdown.
$config['rest']['url'] = 'http://localhost/slim-rest-base';

$config['monolog']['level'] = Monolog\Logger::DEBUG;

return $config;
