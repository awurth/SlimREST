<?php

namespace App\Middleware;

use App\Exception\AccessDeniedException;
use App\Exception\UnauthorizedException;
use Cartalyst\Sentinel\Sentinel;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    protected $role;

    /**
     * @var Sentinel
     */
    protected $sentinel;

    /**
     * Constructor.
     *
     * @param Sentinel $sentinel
     * @param string   $role
     */
    public function __construct(Sentinel $sentinel, $role = null)
    {
        $this->sentinel = $sentinel;
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
