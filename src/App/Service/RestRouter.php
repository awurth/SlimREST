<?php

namespace App\Service;

use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouterInterface;

class RestRouter
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $CRUDMethods;

    public function __construct(RouterInterface $router, $config = [])
    {
        $this->router = $router;
        $this->prefix = isset($config['prefix']) ? $config['prefix'] : '';

        $this->CRUDMethods = [
            'get' => isset($config['get']) ? $config['get'] : true,
            'get_collection' => isset($config['get_collection']) ? $config['get_collection'] : true,
            'post' => isset($config['post']) ? $config['post'] : true,
            'put' => isset($config['put']) ? $config['put'] : true,
            'delete' => isset($config['delete']) ? $config['delete'] : true,
            'delete_collection' => isset($config['delete_collection']) ? $config['delete_collection'] : false
        ];
    }

    /**
     * Get url pattern of a resource
     *
     * @param string $collection
     * @param bool $one
     * @return string
     */
    public function pattern($collection, $one = true)
    {
        return $this->prefix . '/' . $collection . ($one ? '/{id:[0-9]+}' : '');
    }

    /**
     * Get a resource name's singular (removes last character)
     *
     * @param string $collection
     * @return string
     */
    public function singular($collection)
    {
        return substr($collection, 0, -1);
    }

    /**
     * Generate one route
     *
     * @param string $method
     * @param string $collection
     * @param string $controller
     * @param string $singular
     * @return RouteInterface
     */
    public function one($method, $collection, $controller, $singular = null)
    {
        if ($collection === $singular) {
            throw new \InvalidArgumentException('The collection name and its singular must be different');
        }

        if (!$singular) {
            $singular = $this->singular($collection);
        }

        $method = strtolower($method);

        return $this->router->map(
            [$method],
            $this->pattern($collection),
            $controller . ':' . $method . ucfirst($singular)
        )->setName($method . '_' . $singular);
    }

    /**
     * Generate all routes for a resource
     *
     * @param string $collection
     * @param string $controller
     * @param string $singular
     */
    public function crud($collection, $controller, $singular = null)
    {
        if ($this->CRUDMethods['get']) {
            $this->get($collection, $controller, $singular);
        }

        if ($this->CRUDMethods['get_collection']) {
            $this->cget($collection, $controller);
        }

        if ($this->CRUDMethods['post']) {
            $this->post($collection, $controller, $singular);
        }

        if ($this->CRUDMethods['put']) {
            $this->put($collection, $controller, $singular);
        }

        if ($this->CRUDMethods['delete']) {
            $this->delete($collection, $controller, $singular);
        }

        if ($this->CRUDMethods['delete_collection']) {
            $this->cdelete($collection, $controller);
        }
    }

    public function get($collection, $controller, $singular = null)
    {
        return $this->one('GET', $collection, $controller, $singular);
    }

    public function cget($collection, $controller)
    {
        return $this->router->map(
            ['GET'],
            $this->pattern($collection, false),
            $controller . ':get' . ucfirst($collection)
        )->setName('get_' . $collection);
    }

    public function post($collection, $controller, $singular = null)
    {
        return $this->one('POST', $collection, $controller, $singular);
    }

    public function put($collection, $controller, $singular = null)
    {
        return $this->one('PUT', $collection, $controller, $singular);
    }

    public function delete($collection, $controller, $singular = null)
    {
        return $this->one('DELETE', $collection, $controller, $singular);
    }

    public function cdelete($collection, $controller)
    {
        return $this->router->map(
            ['DELETE'],
            $this->pattern($collection, false),
            $controller . ':delete' . ucfirst($collection)
        )->setName('delete_' . $collection);
    }
}