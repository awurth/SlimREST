<?php

namespace App\Middleware;

use App\Service\JWTManager;
use Interop\Container\ContainerInterface;
use Cartalyst\Sentinel\Sentinel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Router;

/**
 * @property Router router
 * @property Sentinel sentinel
 * @property JWTManager jwt
 */
abstract class Middleware
{
    /**
     * Slim application container
     *
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public abstract function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next);

    public function __get($property)
    {
        return $this->container->get($property);
    }
}
