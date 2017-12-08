<?php

use App\Exception\AccessDeniedException;
use App\Exception\UnauthorizedException;
use Slim\Exception\NotFoundException;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Http\Request;
use Slim\Http\Response;

return [

    /**
     * Controller methods signature must be like:
     *
     * public function getCollection($request, $response, $arg1, $arg2, $args3, ...)
     *
     * https://www.slimframework.com/docs/objects/router.html#route-strategies
     */
    'foundHandler' => function ($container) {
        /** @var Request $request */
        $request = $container['request'];
        $container['monolog']->info(sprintf('Matched route "%s /%s"', $request->getMethod(), ltrim($request->getUri()->getPath(), '/')));

        return new RequestResponseArgs();
    },

    /**
     * Returns an error in JSON when a NotFoundException is thrown.
     */
    'notFoundHandler' => function ($container) {
        return function (Request $request, Response $response) use ($container) {
            $container['monolog']->error(sprintf('No resource found for "%s /%s"', $request->getMethod(), ltrim($request->getUri()->getPath(), '/')));

            return $response
                ->withStatus(404)
                ->withJson([
                    'status' => 404,
                    'message' => 'Resource not found.'
                ]);
        };
    },

    /**
     * Returns an error in JSON when the HTTP method is not allowed.
     */
    'notAllowedHandler' => function ($container) {
        return function (Request $request, Response $response, array $methods) use ($container) {
            $allowedMethods = implode(', ', $methods);

            $container['monolog']->error(sprintf(
                'No resource found for "%s /%s": Method not allowed (Allow: %s)',
                $request->getMethod(),
                ltrim($request->getUri()->getPath(), '/'),
                $allowedMethods
            ));

            if ($allowedMethods === 'OPTIONS') {
                throw new NotFoundException($request, $response);
            }

            return $response
                ->withStatus(405)
                ->withHeader('Allow', $allowedMethods)
                ->withJson([
                    'status' => 405,
                    'message' => 'Method must be one of: '.$allowedMethods
                ]);
        };
    },

    /**
     * Returns an error in JSON when an UnauthorizedException is thrown.
     */
    'unauthorizedHandler' => function ($container) {
        return function (Request $request, Response $response, Exception $exception) use ($container) {
            $container['monolog']->debug('Unauthorized, the user is not authenticated', [
                'exception' => $exception
            ]);

            return $response
                ->withStatus($exception->getCode())
                ->withJson([
                    'status' => $exception->getCode(),
                    'message' => $exception->getMessage()
                ]);
        };
    },

    /**
     * Returns an error in JSON when an AccessDeniedException is thrown.
     */
    'accessDeniedHandler' => function ($container) {
        return function (Request $request, Response $response, Exception $exception) use ($container) {
            $container['monolog']->debug('Access denied, the user does not have access to this section', [
                'exception' => $exception
            ]);

            return $response
                ->withStatus($exception->getCode())
                ->withJson([
                    'status' => $exception->getCode(),
                    'message' => $exception->getMessage()
                ]);
        };
    },

    /**
     * Default Slim error handler.
     */
    'errorHandler' => function ($container) {
        return function (Request $request, Response $response, Exception $exception) use ($container) {
            if ($exception instanceof AccessDeniedException) {
                return $container['accessDeniedHandler']($request, $response, $exception);
            }

            if ($exception instanceof UnauthorizedException) {
                return $container['unauthorizedHandler']($request, $response, $exception);
            }

            $container['monolog']->error('Uncaught PHP Exception '.get_class($exception), [
                'exception' => $exception
            ]);

            $message = [
                'status' => 500,
                'message' => 'Internal Server Error.'
            ];

            if ('dev' === $this->getEnvironment()) {
                $message['trace'] = $exception->getTrace();
                $message['message'] = get_class($exception).': '.$exception->getMessage();
            }

            return $response
                ->withStatus(500)
                ->withJson($message);
        };
    },

    /**
     * PHP error handler.
     */
    'phpErrorHandler' => function ($container) {
        return function (Request $request, Response $response, Throwable $error) use ($container) {
            $container['monolog']->critical('Uncaught PHP Exception '.get_class($error), [
                'exception' => $error
            ]);

            $message = [
                'status' => 500,
                'message' => 'Internal Server Error.'
            ];

            if ('dev' === $this->getEnvironment()) {
                $message['trace'] = $error->getTrace();
                $message['message'] = get_class($error).': '.$error->getMessage();
            }

            return $response
                ->withStatus(500)
                ->withJson($message);
        };
    }

];
