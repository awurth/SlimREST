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
        $headers = [
            'origin'         => 'Access-Control-Allow-Origin',
            'methods'        => 'Access-Control-Allow-Methods',
            'allow_headers'  => 'Access-Control-Allow-Headers',
            'expose_headers' => 'Access-Control-Expose-Headers',
            'max_age'        => 'Access-Control-Max-Age',
        ];

        foreach ($headers as $key => $name) {
            if (isset($this->options[$key])) {
                $response = $response->withHeader($name, $this->options[$key]);
            }
        }

        return $next($request, $response);
    }
}
