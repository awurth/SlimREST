<?php

namespace App\Security\Middleware;

use App\Core\Middleware\MiddlewareInterface;
use App\Security\Exception\AccessDeniedException;
use App\Security\Exception\UnauthorizedException;
use App\Security\Jwt\Manager as JwtManager;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    protected $role;

    /**
     * @var JwtManager
     */
    protected $jwt;

    /**
     * Constructor.
     *
     * @param JwtManager $jwt
     * @param string     $role
     */
    public function __construct(JwtManager $jwt, $role = null)
    {
        $this->jwt = $jwt;
        $this->role = $role;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->jwt->getAccessToken()) {
            throw new UnauthorizedException();
        }

        if ($this->role) {
            if (!$this->jwt->getAccessToken()->user->inRole($this->role)) {
                throw new AccessDeniedException('Access denied: User must have role ' . $this->role);
            }
        }

        return $next($request, $response);
    }
}
