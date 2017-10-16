<?php

namespace App\Security\Middleware;

use App\Core\Middleware\MiddlewareInterface;
use App\Security\Jwt\Manager as JwtManager;
use Slim\Http\Request;
use Slim\Http\Response;

class JwtMiddleware implements MiddlewareInterface
{
    /**
     * @var JwtManager
     */
    protected $jwt;

    /**
     * Constructor.
     *
     * @param JwtManager $jwt
     */
    public function __construct(JwtManager $jwt)
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
