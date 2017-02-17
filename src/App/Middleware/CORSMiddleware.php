<?php

namespace App\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CORSMiddleware
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $this->config['origin'])
            ->withHeader('Access-Control-Allow-Headers', $this->config['headers'])
            ->withHeader('Access-Control-Allow-Methods', $this->config['methods'])
            ->withHeader('Access-Control-Max-Age', $this->config['max_age']);

        return $next($request, $response);
    }
}
