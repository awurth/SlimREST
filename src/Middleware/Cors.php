<?php

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

class Cors implements MiddlewareInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $this->options['origin'])
            ->withHeader('Access-Control-Allow-Headers', $this->options['allow_headers'])
            ->withHeader('Access-Control-Expose-Headers', $this->options['expose_headers'])
            ->withHeader('Access-Control-Allow-Methods', $this->options['methods'])
            ->withHeader('Access-Control-Max-Age', $this->options['max_age']);

        return $next($request, $response);
    }
}
