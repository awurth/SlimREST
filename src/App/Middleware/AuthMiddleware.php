<?php

namespace App\Middleware;

use App\Exception\AccessDeniedException;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthMiddleware extends Middleware
{
    /**
     * @var string
     */
    private $role;

    public function __construct(ContainerInterface $container, $role = '')
    {
        parent::__construct($container);

        $this->role = $role;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->jwt->getAccessToken()) {
            return $response
                ->withStatus(401)
                ->withJson([
                    'status' => 401,
                    'message' => 'Unauthorized'
                ]);
        }

        if ($this->role) {
            if (!$this->jwt->getAccessToken()->user->inRole($this->role)) {
                throw new AccessDeniedException('Access denied: User must have role ' . $this->role);
            }
        }

        return $next($request, $response);
    }
}
