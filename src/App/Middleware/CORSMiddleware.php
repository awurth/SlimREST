<?php

namespace App\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CORSMiddleware extends Middleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $settings = $this->container->settings['cors'];

        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $settings['origin'])
            ->withHeader('Access-Control-Allow-Headers', $settings['headers'])
            ->withHeader('Access-Control-Allow-Methods', $settings['methods'])
            ->withHeader('Access-Control-Max-Age', $settings['max_age']);

        return $next($request, $response);
    }
}
