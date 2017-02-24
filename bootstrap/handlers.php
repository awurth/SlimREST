<?php

use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Exception\NotFoundException;
use App\Exception\AccessDeniedException;

/**
 * Return error in JSON when a NotFoundException is thrown
 */
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        return $response
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
        $allowedMethods = implode(', ', $methods);

        if ($allowedMethods === 'OPTIONS') {
            throw new NotFoundException($request, $response);
        }

        return $response
            ->withStatus(405)
            ->withHeader('Allow', $allowedMethods)
            ->withJson([
                'status' => 405,
                'message' => 'Method must be one of: ' . $allowedMethods
            ]);
    };
};

/**
 * Return error in JSON when an AccessDeniedException is thrown
 */
$container['accessDeniedHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        return $response
            ->withStatus(403)
            ->withJson([
                'status' => 403,
                'message' => $exception->getMessage()
            ]);
    };
};

/**
 * Default Slim error handler
 */
$container['errorHandler'] = function ($container) use ($config) {
    return function ($request, $response, $exception) use ($container, $config) {
        if ($exception instanceof AccessDeniedException) {
            return $container['accessDeniedHandler']($request, $response, $exception);
        }

        $message = [
            'status' => 500,
            'message' => 'Internal Server Error'
        ];

        if ($config['settings']['displayErrorDetails']) {
            $message['trace'] = $exception->getTrace();
            $message['message'] = get_class($exception) . ': ' . $exception->getMessage();
        }

        return $response
            ->withStatus(500)
            ->withJson($message);
    };
};

/**
 * PHP error handler
 */
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

        return $response
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
