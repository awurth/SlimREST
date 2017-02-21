<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthMiddleware extends Middleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->jwt->getAccessToken()) {
            return $response
                ->withStatus(401)
                ->withJson([
                    'status' => 401,
                    'message' => 'Access Denied'
                ]);
        }

        return $next($request, $response);
    }
}
