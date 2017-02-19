<?php

use Slim\Handlers\Strategies\RequestResponseArgs;

/**
 * Return error in JSON if when a NotFoundException is thrown
 */
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        return $container['response']
            ->withStatus(404)
            ->withJson([
                'status' => 404,
                'message' => 'Resource not found'
            ]);
    };
};

/**
 * Return error in JSON when HTTP method is not allowed
 */
$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        return $container['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withJson([
                'status' => 405,
                'message' => 'Method must be one of: ' . implode(', ', $methods)
            ]);
    };
};

$container['errorHandler'] = function ($container) use ($config) {
    return function ($request, $response, $exception) use ($container, $config) {
        $message = [
            'status' => 500,
            'message' => 'Internal Server Error'
        ];

        if ($config['settings']['displayErrorDetails']) {
            $message['trace'] = $exception->getTrace();
            $message['message'] = get_class($exception) . ': ' . $exception->getMessage();
        }

        return $container['response']
            ->withStatus(500)
            ->withJson($message);
    };
};

$container['phpErrorHandler'] = function ($container) use ($config) {
    return function ($request, $response, $error) use ($container, $config) {
        $message = [
            'status' => 500,
            'message' => 'Internal Server Error'
        ];

        if ($config['settings']['displayErrorDetails']) {
            $message['trace'] = $error->getTrace();
            $message['message'] = get_class($error) . ': ' . $error->getMessage();
        }

        return $container['response']
            ->withStatus(500)
            ->withJson($message);
    };
};

/**
 * Controller functions signature must be like:
 *
 * public function getCollection($request, $response, $arg1, $arg2, $args3, ...)
 *
 */
$container['foundHandler'] = function () {
    return new RequestResponseArgs();
};
