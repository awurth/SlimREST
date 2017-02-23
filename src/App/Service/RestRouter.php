<?php

namespace App\Service;

use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouterInterface;

class RestRouter
{
    const DEFAULT_KEY = 'id';
    const DEFAULT_REQUIREMENT = '[0-9]+';

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
            'get' => isset($config['crud']['get']) ? $config['crud']['get'] : true,
            'get_collection' => isset($config['crud']['get_collection']) ? $config['crud']['get_collection'] : true,
            'post' => isset($config['crud']['post']) ? $config['crud']['post'] : true,
            'put' => isset($config['crud']['put']) ? $config['crud']['put'] : true,
            'delete' => isset($config['crud']['delete']) ? $config['crud']['delete'] : true,
            'delete_collection' => isset($config['crud']['delete_collection']) ? $config['crud']['delete_collection'] : false
        ];
    }

    /**
     * Get url pattern of a resource
     *
     * @param string $collection
     * @param bool $one
     * @param string $key
     * @param string $requirement
     * @return string
     */
    public function pattern($collection, $one = true, $key = self::DEFAULT_KEY, $requirement = self::DEFAULT_REQUIREMENT)
    {
        if ($requirement) {
            $requirement = ':' . $requirement;
        }

        return $this->prefix . '/' . $collection . ($one ? '/{' . $key . $requirement . '}' : '');
    }

    /**
     * Get url pattern of a sub resource
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param bool $one
     * @param string $parentSingular
     * @param string $subSingular
     * @param string $parentKey
     * @param string $parentRequirement
     * @param string $subKey
     * @param string $subRequirement
     * @return string
     */
    public function subPattern($parentCollection, $subCollection, $one = true, $parentSingular = null, $subSingular = null, $parentKey = self::DEFAULT_KEY, $parentRequirement = self::DEFAULT_REQUIREMENT, $subKey = self::DEFAULT_KEY, $subRequirement = self::DEFAULT_REQUIREMENT)
    {
        if ($parentKey === self::DEFAULT_KEY) {
            $parentKey = $parentSingular ? $parentSingular . '_' . $parentKey : $this->singular($parentCollection) . '_' . $parentKey;
        }

        if ($subKey === self::DEFAULT_KEY) {
            $subKey = $subSingular ? $subSingular . '_' . $subKey : $this->singular($subCollection) . '_' . $subKey;
        }

        if ($subRequirement) {
            $subRequirement = ':' . $subRequirement;
        }

        $parent = $this->pattern($parentCollection, true, $parentKey, $parentRequirement);

        return $parent . '/' . $subCollection . ($one ? '/{' . $subKey . $subRequirement . '}' : '');
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
     * Generate a route for a single resource
     *
     * @param string $method
     * @param string $collection
     * @param string $controller
     * @param string $singular
     * @param string $key
     * @param string $requirement
     * @return RouteInterface
     */
    public function one($method, $collection, $controller, $singular = null, $key = self::DEFAULT_KEY, $requirement = self::DEFAULT_REQUIREMENT)
    {
        if (!$method) {
            throw new \InvalidArgumentException('The method is required');
        }

        if ($collection === $singular) {
            throw new \InvalidArgumentException('The collection name and its singular must be different');
        }

        if (!$singular) {
            $singular = $this->singular($collection);
        }

        $method = strtolower($method);

        return $this->router->map(
            [$method],
            $this->pattern($collection, true, $key, $requirement),
            $controller . ':' . $method . ucfirst($singular)
        )->setName($method . '_' . $singular);
    }

    /**
     * Generate a single route for a sub resource
     *
     * @param string $method
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param string $parentSingular
     * @param string $subSingular
     * @param string $parentKey
     * @param string $parentRequirement
     * @param string $subKey
     * @param string $subRequirement
     * @return RouteInterface
     */
    public function subOne($method, $parentCollection, $subCollection, $controller, $parentSingular = null, $subSingular = null, $parentKey = self::DEFAULT_KEY, $parentRequirement = self::DEFAULT_REQUIREMENT, $subKey = self::DEFAULT_KEY, $subRequirement = self::DEFAULT_REQUIREMENT)
    {
        if (!$method) {
            throw new \InvalidArgumentException('The method is required');
        }

        if ($parentCollection === $parentSingular) {
            throw new \InvalidArgumentException('The parent collection name and its singular must be different');
        }

        if ($subCollection === $subSingular) {
            throw new \InvalidArgumentException('The sub collection name and its singular must be different');
        }

        if (!$parentSingular) {
            $parentSingular = $this->singular($parentCollection);
        }

        if (!$subSingular) {
            $subSingular = $this->singular($subCollection);
        }

        $method = strtolower($method);

        return $this->router->map(
            [$method],
            $this->subPattern($parentCollection, $subCollection, true, $parentSingular, $subSingular, $parentKey, $parentRequirement, $subKey, $subRequirement),
            $controller . ':' . $method . ucfirst($parentSingular) . ucfirst($subSingular)
        )->setName($method . '_' . $parentSingular . '_' . $subSingular);
    }

    /**
     * Generate a route for a collection
     *
     * @param string $method
     * @param string $collection
     * @param string $controller
     * @return RouteInterface
     */
    public function collection($method, $collection, $controller)
    {
        $method = strtolower($method);

        return $this->router->map(
            [$method],
            $this->pattern($collection, false),
            $controller . ':' . $method . ucfirst($collection)
        )->setName($method . '_' . $collection);
    }

    public function subCollection($method, $parentCollection, $subCollection, $controller, $parentSingular = null, $parentKey = self::DEFAULT_KEY, $parentRequirement = self::DEFAULT_REQUIREMENT)
    {
        if (!$parentSingular) {
            $parentSingular = $this->singular($parentCollection);
        }

        return $this->router->map(
            [$method],
            $this->subPattern($parentCollection, $subCollection, false, $parentSingular, null, $parentKey, $parentRequirement),
            $controller . ':get' . ucfirst($parentSingular) . ucfirst($subCollection)
        )->setName('get_' . $parentSingular . '_' . $subCollection);
    }

    /**
     * Generate all routes for a resource
     *
     * @param string $collection
     * @param string $controller
     * @param string $singular
     * @param array $middleware
     * @param string $key
     * @param string $requirement
     * @return RouteInterface[]
     */
    public function CRUD($collection, $controller, $singular = null, array $middleware = [], $key = self::DEFAULT_KEY, $requirement = self::DEFAULT_REQUIREMENT)
    {
        $routes = [];

        if ($this->CRUDMethods['get']) {
            $routes['get'] = $this->get($collection, $controller, $singular, $key, $requirement);
        }

        if ($this->CRUDMethods['get_collection']) {
            $routes['cget'] = $this->cget($collection, $controller);
        }

        if ($this->CRUDMethods['post']) {
            $routes['post'] = $this->post($collection, $controller, $singular);
        }

        if ($this->CRUDMethods['put']) {
            $routes['put'] = $this->put($collection, $controller, $singular, $key, $requirement);
        }

        if ($this->CRUDMethods['delete']) {
            $routes['delete'] = $this->delete($collection, $controller, $singular, $key, $requirement);
        }

        if ($this->CRUDMethods['delete_collection']) {
            $routes['cdelete'] = $this->cdelete($collection, $controller);
        }

        foreach ($routes as $route) {
            foreach ($middleware as $m) {
                $route->add($m);
            }
        }

        return $routes;
    }

    /**
     * Generate all routes for a sub resource
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param string $parentSingular
     * @param string $subSingular
     * @param array $middleware
     * @param string $parentKey
     * @param string $parentRequirement
     * @param string $subKey
     * @param string $subRequirement
     * @return RouteInterface[]
     */
    public function subCRUD($parentCollection, $subCollection, $controller, $parentSingular = null, $subSingular = null, array $middleware = [], $parentKey = self::DEFAULT_KEY, $parentRequirement = self::DEFAULT_REQUIREMENT, $subKey = self::DEFAULT_KEY, $subRequirement = self::DEFAULT_REQUIREMENT)
    {
        $routes = [];

        if ($this->CRUDMethods['get']) {
            $routes['get'] = $this->getSub($parentCollection, $subCollection, $controller, $parentSingular, $subSingular, $parentKey, $parentRequirement, $subKey, $subRequirement);
        }

        if ($this->CRUDMethods['get_collection']) {
            $routes['cget'] = $this->cgetSub($parentCollection, $subCollection, $controller, $parentSingular, $parentKey, $parentRequirement);
        }

        if ($this->CRUDMethods['post']) {
            $routes['post'] = $this->postSub($parentCollection, $subCollection, $controller, $parentSingular, $subSingular, $parentKey, $parentRequirement);
        }

        if ($this->CRUDMethods['put']) {
            $routes['put'] = $this->putSub($parentCollection, $subCollection, $controller, $parentSingular, $subSingular, $parentKey, $parentRequirement, $subKey, $subRequirement);
        }

        if ($this->CRUDMethods['delete']) {
            $routes['delete'] = $this->deleteSub($parentCollection, $subCollection, $controller, $parentSingular, $subSingular, $parentKey, $parentRequirement, $subKey, $subRequirement);
        }

        if ($this->CRUDMethods['delete_collection']) {
            $routes['cdelete'] = $this->cdeleteSub($parentCollection, $subCollection, $controller, $parentSingular, $parentKey, $parentRequirement);
        }

        foreach ($routes as $route) {
            foreach ($middleware as $m) {
                $route->add($m);
            }
        }

        return $routes;
    }

    /**
     * Generate a route for a single resource with GET method
     *
     * @param string $collection
     * @param string $controller
     * @param string $singular
     * @param string $key
     * @param string $requirement
     * @return RouteInterface
     */
    public function get($collection, $controller, $singular = null, $key = self::DEFAULT_KEY, $requirement = self::DEFAULT_REQUIREMENT)
    {
        return $this->one('GET', $collection, $controller, $singular, $key, $requirement);
    }

    /**
     * Generate a route for a single sub resource with GET method
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param string $parentSingular
     * @param string $subSingular
     * @param string $parentKey
     * @param string $parentRequirement
     * @param string $subKey
     * @param string $subRequirement
     * @return RouteInterface
     */
    public function getSub($parentCollection, $subCollection, $controller, $parentSingular = null, $subSingular = null, $parentKey = self::DEFAULT_KEY, $parentRequirement = self::DEFAULT_REQUIREMENT, $subKey = self::DEFAULT_KEY, $subRequirement = self::DEFAULT_REQUIREMENT)
    {
        return $this->subOne('GET', $parentCollection, $subCollection, $controller, $parentSingular, $subSingular, $parentKey, $parentRequirement, $subKey, $subRequirement);
    }

    /**
     * Generate a route for a collection with GET method
     *
     * @param string $collection
     * @param string $controller
     * @return RouteInterface
     */
    public function cget($collection, $controller)
    {
        return $this->collection('GET', $collection, $controller);
    }

    /**
     * Generate a route for a sub resource collection with GET method
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param string $parentSingular
     * @param string $parentKey
     * @param string $parentRequirement
     * @return RouteInterface
     */
    public function cgetSub($parentCollection, $subCollection, $controller, $parentSingular = null, $parentKey = self::DEFAULT_KEY, $parentRequirement = self::DEFAULT_REQUIREMENT)
    {
        return $this->subCollection('GET', $parentCollection, $subCollection, $controller, $parentSingular, $parentKey, $parentRequirement);
    }

    /**
     * Generate a route for a collection with POST method
     *
     * @param string $collection
     * @param string $controller
     * @param string $singular
     * @return RouteInterface
     */
    public function post($collection, $controller, $singular = null)
    {
        if (!$singular) {
            $singular = $this->singular($collection);
        }

        return $this->router->map(
            ['POST'],
            $this->pattern($collection, false),
            $controller . ':post' . ucfirst($singular)
        )->setName('post_' . $singular);
    }

    /**
     * Generate a route for a sub resource collection with POST method
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param string $parentSingular
     * @param string $subSingular
     * @param string $parentKey
     * @param string $parentRequirement
     * @return RouteInterface
     */
    public function postSub($parentCollection, $subCollection, $controller, $parentSingular = null, $subSingular = null, $parentKey = self::DEFAULT_KEY, $parentRequirement = self::DEFAULT_REQUIREMENT)
    {
        if (!$parentSingular) {
            $parentSingular = $this->singular($parentCollection);
        }

        if (!$subSingular) {
            $subSingular = $this->singular($subCollection);
        }

        return $this->router->map(
            ['POST'],
            $this->subPattern($parentCollection, $subCollection, false, $parentSingular, $subSingular, $parentKey, $parentRequirement),
            $controller . ':post' . ucfirst($parentSingular) . ucfirst($subSingular)
        )->setName('post_' . $parentSingular . '_' . $subSingular);
    }

    /**
     * Generate a route for a single resource with PUT method
     *
     * @param string $collection
     * @param string $controller
     * @param string $singular
     * @param string $key
     * @param string $requirement
     * @return RouteInterface
     */
    public function put($collection, $controller, $singular = null, $key = self::DEFAULT_KEY, $requirement = self::DEFAULT_REQUIREMENT)
    {
        return $this->one('PUT', $collection, $controller, $singular, $key, $requirement);
    }

    /**
     * Generate a route for a single sub resource with PUT method
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param string $parentSingular
     * @param string $subSingular
     * @param string $parentKey
     * @param string $parentRequirement
     * @param string $subKey
     * @param string $subRequirement
     * @return RouteInterface
     */
    public function putSub($parentCollection, $subCollection, $controller, $parentSingular = null, $subSingular = null, $parentKey = self::DEFAULT_KEY, $parentRequirement = self::DEFAULT_REQUIREMENT, $subKey = self::DEFAULT_KEY, $subRequirement = self::DEFAULT_REQUIREMENT)
    {
        return $this->subOne('PUT', $parentCollection, $subCollection, $controller, $parentSingular, $subSingular, $parentKey, $parentRequirement, $subKey, $subRequirement);
    }

    /**
     * Generate a route for a single resource with DELETE method
     *
     * @param string $collection
     * @param string $controller
     * @param string $singular
     * @param string $key
     * @param string $requirement
     * @return RouteInterface
     */
    public function delete($collection, $controller, $singular = null, $key = self::DEFAULT_KEY, $requirement = self::DEFAULT_REQUIREMENT)
    {
        return $this->one('DELETE', $collection, $controller, $singular, $key, $requirement);
    }

    /**
     * Generate a route for a single sub resource with DELETE method
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param string $parentSingular
     * @param string $subSingular
     * @param string $parentKey
     * @param string $parentRequirement
     * @param string $subKey
     * @param string $subRequirement
     * @return RouteInterface
     */
    public function deleteSub($parentCollection, $subCollection, $controller, $parentSingular = null, $subSingular = null, $parentKey = self::DEFAULT_KEY, $parentRequirement = self::DEFAULT_REQUIREMENT, $subKey = self::DEFAULT_KEY, $subRequirement = self::DEFAULT_REQUIREMENT)
    {
        return $this->subOne('DELETE', $parentCollection, $subCollection, $controller, $parentSingular, $subSingular, $parentKey, $parentRequirement, $subKey, $subRequirement);
    }

    /**
     * Generate a route for a collection with DELETE method
     *
     * @param string $collection
     * @param string $controller
     * @return RouteInterface
     */
    public function cdelete($collection, $controller)
    {
        return $this->collection('DELETE', $collection, $controller);
    }

    /**
     * Generate a route for a sub resource collection with DELETE method
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param string $parentSingular
     * @param string $parentKey
     * @param string $parentRequirement
     * @return RouteInterface
     */
    public function cdeleteSub($parentCollection, $subCollection, $controller, $parentSingular = null, $parentKey = self::DEFAULT_KEY, $parentRequirement = self::DEFAULT_REQUIREMENT)
    {
        return $this->subCollection('DELETE', $parentCollection, $subCollection, $controller, $parentSingular, $parentKey, $parentRequirement);
    }
}