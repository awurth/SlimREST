<?php

use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Exception\AccessDeniedException;
use App\Exception\UnauthorizedException;

/**
 * Return error in JSON when a NotFoundException is thrown
 */
$container['notFoundHandler'] = function () {
    return function (Request $request, Response $response) {
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
$container['notAllowedHandler'] = function () {
    return function (Request $request, Response $response, $methods) {
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
 * Return error in JSON when an UnauthorizedException is thrown
 */
$container['unauthorizedHandler'] = function () {
    return function (Request $request, Response $response, Exception $exception) {
        return $response
            ->withStatus(401)
            ->withJson([
                'status' => 401,
                'message' => $exception->getMessage()
            ]);
    };
};

/**
 * Return error in JSON when an AccessDeniedException is thrown
 */
$container['accessDeniedHandler'] = function () {
    return function (Request $request, Response $response, Exception $exception) {
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
    return function (Request $request, Response $response, Exception $exception) use ($container, $config) {
        if ($exception instanceof AccessDeniedException) {
            return $container['accessDeniedHandler']($request, $response, $exception);
        }

        if ($exception instanceof UnauthorizedException) {
            return $container['unauthorizedHandler']($request, $response, $exception);
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
$container['phpErrorHandler'] = function () use ($config) {
    return function (Request $request, Response $response, Throwable $error) use ($config) {
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
