<?php

namespace App\Middleware;

use App\Service\JWTManager;
use Interop\Container\ContainerInterface;

use Cartalyst\Sentinel\Sentinel;
use Slim\Router;
use Slim\Views\Twig;

/**
 * @property Twig view
 * @property Router router
 * @property Sentinel sentinel
 * @property JWTManager jwt
 */
class Middleware
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

    public function __get($property)
    {
        return $this->container->get($property);
    }
}
