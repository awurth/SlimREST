<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class JWTMiddleware extends Middleware
{
    public function __invoke(ServerRequestInterface $request, $response, $next)
    {
        if ($request->hasHeader('Authorization')) {
            $header = $request->getHeader('Authorization');
            $this->jwt->checkToken(substr($header[0], 7));
        }

        return $next($request, $response);
    }
}
