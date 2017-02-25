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
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $requirement;

    /**
     * @var array
     */
    protected $CRUDMethods;

    public function __construct(RouterInterface $router, $config = [])
    {
        $this->router = $router;

        $this->prefix = $config['prefix'] ?? '';
        $this->key = $config['key'] ?? self::DEFAULT_KEY;
        $this->requirement = $config['requirement'] ?? self::DEFAULT_REQUIREMENT;

        $this->CRUDMethods = [
            'get' => $config['crud']['get'] ?? true,
            'get_collection' => $config['crud']['get_collection'] ?? true,
            'post' => $config['crud']['post'] ?? true,
            'put' => $config['crud']['put'] ?? true,
            'delete' => $config['crud']['delete'] ?? true,
            'delete_collection' => $config['crud']['delete_collection'] ?? false
        ];
    }

    /**
     * Get url pattern of a resource
     *
     * @param string $collection
     * @param bool $one
     * @param array $options
     * @return string
     */
    public function pattern($collection, $one = true, array $options = [])
    {
        $key = $options['key'] ?? $this->key;
        $requirement = $options['requirement'] ?? $this->requirement;

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
     * @param array $options
     * @return string
     */
    public function subPattern($parentCollection, $subCollection, $one = true, array $options = [])
    {
        $parentSingular = $options['parent_singular'] ?? $this->singular($parentCollection);
        $subSingular = $options['sub_singular'] ?? $this->singular($subCollection);

        $parentKey = $options['parent_key'] ?? $parentSingular . '_' . $this->key;
        $subKey = $options['sub_key'] ?? $subSingular . '_' . $this->key;

        $parentRequirement = $options['parent_requirement'] ?? $this->requirement;
        $subRequirement = $options['sub_requirement'] ?? $this->requirement;

        if ($subRequirement) {
            $subRequirement = ':' . $subRequirement;
        }

        $parent = $this->pattern($parentCollection, true, [
            'key' => $parentKey,
            'requirement' => $parentRequirement
        ]);

        return $parent . '/' . $subCollection . ($one ? '/{' . $subKey . $subRequirement . '}' : '');
    }

    /**
     * Generate a route for a single resource
     *
     * @param string $method
     * @param string $collection
     * @param string $controller
     * @param array $options
     * @return RouteInterface
     */
    public function one($method, $collection, $controller, array $options = [])
    {
        if (!$method) {
            throw new \InvalidArgumentException('The method is required');
        }

        $singular = $options['singular'] ?? $this->singular($collection);

        if ($collection === $singular) {
            throw new \InvalidArgumentException('The collection name and its singular must be different');
        }

        $method = strtolower($method);

        return $this->router->map(
            [$method],
            $this->pattern($collection, true, $options),
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
     * @param array $options
     * @return RouteInterface
     */
    public function subOne($method, $parentCollection, $subCollection, $controller, array $options = [])
    {
        if (!$method) {
            throw new \InvalidArgumentException('The method is required');
        }

        $parentSingular = $options['parent_singular'] ?? $this->singular($parentCollection);
        $subSingular = $options['sub_singular'] ?? $this->singular($subCollection);

        if ($parentCollection === $parentSingular) {
            throw new \InvalidArgumentException('The parent collection name and its singular must be different');
        }

        if ($subCollection === $subSingular) {
            throw new \InvalidArgumentException('The sub collection name and its singular must be different');
        }

        $method = strtolower($method);

        return $this->router->map(
            [$method],
            $this->subPattern($parentCollection, $subCollection, true, $options),
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

    /**
     * Generate a route for a sub collection
     *
     * @param string $method
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array $options
     * @return RouteInterface
     */
    public function subCollection($method, $parentCollection, $subCollection, $controller, array $options = [])
    {
        $parentSingular = $options['parent_singular'] ?? $this->singular($parentCollection);

        return $this->router->map(
            [$method],
            $this->subPattern($parentCollection, $subCollection, false, $options),
            $controller . ':get' . ucfirst($parentSingular) . ucfirst($subCollection)
        )->setName('get_' . $parentSingular . '_' . $subCollection);
    }

    /**
     * Generate all routes for a resource
     *
     * @param string $collection
     * @param string $controller
     * @param array $middleware
     * @param array $options
     * @return RouteInterface[]
     */
    public function CRUD($collection, $controller, array $middleware = [], array $options = [])
    {
        $routes = [];

        if ($this->CRUDMethods['get']) {
            $routes['get'] = $this->get($collection, $controller, $options);
        }

        if ($this->CRUDMethods['get_collection']) {
            $routes['cget'] = $this->cget($collection, $controller);
        }

        if ($this->CRUDMethods['post']) {
            $routes['post'] = $this->post($collection, $controller, $options['singular'] ?? '');
        }

        if ($this->CRUDMethods['put']) {
            $routes['put'] = $this->put($collection, $controller, $options);
        }

        if ($this->CRUDMethods['delete']) {
            $routes['delete'] = $this->delete($collection, $controller, $options);
        }

        if ($this->CRUDMethods['delete_collection']) {
            $routes['cdelete'] = $this->cdelete($collection, $controller);
        }

        if (!empty($middleware)) {
            foreach ($routes as $route) {
                foreach ($middleware as $m) {
                    $route->add($m);
                }
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
     * @param array $middleware
     * @param array $options
     * @return RouteInterface[]
     */
    public function subCRUD($parentCollection, $subCollection, $controller, array $middleware = [], array $options = [])
    {
        $routes = [];

        if ($this->CRUDMethods['get']) {
            $routes['get'] = $this->getSub($parentCollection, $subCollection, $controller, $options);
        }

        if ($this->CRUDMethods['get_collection']) {
            $routes['cget'] = $this->cgetSub($parentCollection, $subCollection, $controller, $options);
        }

        if ($this->CRUDMethods['post']) {
            $routes['post'] = $this->postSub($parentCollection, $subCollection, $controller, $options);
        }

        if ($this->CRUDMethods['put']) {
            $routes['put'] = $this->putSub($parentCollection, $subCollection, $controller, $options);
        }

        if ($this->CRUDMethods['delete']) {
            $routes['delete'] = $this->deleteSub($parentCollection, $subCollection, $controller, $options);
        }

        if ($this->CRUDMethods['delete_collection']) {
            $routes['cdelete'] = $this->cdeleteSub($parentCollection, $subCollection, $controller, $options);
        }

        if (!empty($middleware)) {
            foreach ($routes as $route) {
                foreach ($middleware as $m) {
                    $route->add($m);
                }
            }
        }

        return $routes;
    }

    /**
     * Generate a route for a single resource with GET method
     *
     * @param string $collection
     * @param string $controller
     * @param array $options
     * @return RouteInterface
     */
    public function get($collection, $controller, array $options = [])
    {
        return $this->one('GET', $collection, $controller, $options);
    }

    /**
     * Generate a route for a single sub resource with GET method
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array $options
     * @return RouteInterface
     */
    public function getSub($parentCollection, $subCollection, $controller, array $options = [])
    {
        return $this->subOne('GET', $parentCollection, $subCollection, $controller, $options);
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
     * @param array $options
     * @return RouteInterface
     */
    public function cgetSub($parentCollection, $subCollection, $controller, array $options = [])
    {
        return $this->subCollection('GET', $parentCollection, $subCollection, $controller, $options);
    }

    /**
     * Generate a route for a collection with POST method
     *
     * @param string $collection
     * @param string $controller
     * @param string $singular
     * @return RouteInterface
     */
    public function post($collection, $controller, $singular = '')
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
     * @param array $options
     * @return RouteInterface
     */
    public function postSub($parentCollection, $subCollection, $controller, array $options = [])
    {
        $parentSingular = $options['parent_singular'] ?? $this->singular($parentCollection);
        $subSingular = $options['sub_singular'] ?? $this->singular($subCollection);

        return $this->router->map(
            ['POST'],
            $this->subPattern($parentCollection, $subCollection, false, $options),
            $controller . ':post' . ucfirst($parentSingular) . ucfirst($subSingular)
        )->setName('post_' . $parentSingular . '_' . $subSingular);
    }

    /**
     * Generate a route for a single resource with PUT method
     *
     * @param string $collection
     * @param string $controller
     * @param array $options
     * @return RouteInterface
     */
    public function put($collection, $controller, array $options = [])
    {
        return $this->one('PUT', $collection, $controller, $options);
    }

    /**
     * Generate a route for a single sub resource with PUT method
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array $options
     * @return RouteInterface
     */
    public function putSub($parentCollection, $subCollection, $controller, array $options = [])
    {
        return $this->subOne('PUT', $parentCollection, $subCollection, $controller, $options);
    }

    /**
     * Generate a route for a single resource with DELETE method
     *
     * @param string $collection
     * @param string $controller
     * @param array $options
     * @return RouteInterface
     */
    public function delete($collection, $controller, array $options = [])
    {
        return $this->one('DELETE', $collection, $controller, $options);
    }

    /**
     * Generate a route for a single sub resource with DELETE method
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array $options
     * @return RouteInterface
     */
    public function deleteSub($parentCollection, $subCollection, $controller, array $options = [])
    {
        return $this->subOne('DELETE', $parentCollection, $subCollection, $controller, $options);
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
     * @param array $options
     * @return RouteInterface
     */
    public function cdeleteSub($parentCollection, $subCollection, $controller, array $options = [])
    {
        return $this->subCollection('DELETE', $parentCollection, $subCollection, $controller, $options);
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
}
