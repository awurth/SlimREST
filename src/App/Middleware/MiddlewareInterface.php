<?php

namespace App\Middleware;

use App\Service\JWTManager;
use Cartalyst\Sentinel\Sentinel;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * @property Router router
 * @property Sentinel sentinel
 * @property JWTManager jwt
 */
interface MiddlewareInterface
{
    /**
     * Method call when the class is user as a function.
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, callable $next);
}
