<?php

use Monolog\Logger;

$config = require __DIR__ . '/services.php';

// Used for generating links in API routes markdown.
$config['rest']['url'] = 'http://localhost/slim-rest-base';

$config['monolog']['level'] = Logger::DEBUG;

return $config;
