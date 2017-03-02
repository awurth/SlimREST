<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CORSMiddleware extends Middleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $this->cors['origin'])
            ->withHeader('Access-Control-Allow-Headers', $this->cors['allow_headers'])
            ->withHeader('Access-Control-Expose-Headers', $this->cors['expose_headers'])
            ->withHeader('Access-Control-Allow-Methods', $this->cors['methods'])
            ->withHeader('Access-Control-Max-Age', $this->cors['max_age']);

        return $next($request, $response);
    }
}
