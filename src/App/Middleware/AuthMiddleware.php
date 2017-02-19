<?php

namespace App\Middleware;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
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
