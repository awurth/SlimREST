<?php

namespace App\Middleware;

use App\Service\JWTManager;
use Slim\Http\Request;
use Slim\Http\Response;

class JwtMiddleware implements MiddlewareInterface
{
    /**
     * @var JWTManager
     */
    protected $jwt;

    /**
     * Constructor.
     *
     * @param JWTManager $jwt
     */
    public function __construct(JWTManager $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if ($request->hasHeader('Authorization')) {
            $header = $request->getHeader('Authorization');
            $this->jwt->checkAccessToken(substr($header[0], 7));
        }

        return $next($request, $response);
    }
}
