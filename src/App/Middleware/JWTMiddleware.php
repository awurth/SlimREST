<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class JWTMiddleware extends Middleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if ($request->hasHeader('Authorization')) {
            $header = $request->getHeader('Authorization');
            $this->jwt->checkAccessToken(substr($header[0], 7));
        }

        return $next($request, $response);
    }
}
