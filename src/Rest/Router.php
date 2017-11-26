<?php

namespace App\Rest;

use InvalidArgumentException;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouterInterface;

class Router
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

    /**
     * Constructor.
     *
     * @param RouterInterface $router
     * @param array           $config
     */
    public function __construct(RouterInterface $router, array $config = [])
    {
        $this->router = $router;

        $this->prefix = $config['prefix'] ?? '';
        $this->key = $config['key'] ?? self::DEFAULT_KEY;
        $this->requirement = $config['requirement'] ?? self::DEFAULT_REQUIREMENT;

        $this->CRUDMethods = [
            'get' => $config['crud']['get'] ?? true,
            'cget' => $config['crud']['get_collection'] ?? true,
            'post' => $config['crud']['post'] ?? true,
            'put' => $config['crud']['put'] ?? true,
            'delete' => $config['crud']['delete'] ?? true,
            'cdelete' => $config['crud']['delete_collection'] ?? false
        ];
    }

    /**
     * Gets the URL pattern of a resource.
     *
     * @param string $collection
     * @param bool   $one
     * @param array  $options
     *
     * @return string
     */
    public function pattern(string $collection, bool $one = true, array $options = [])
    {
        $key = $options['key'] ?? $this->key;
        $requirement = $options['requirement'] ?? $this->requirement;

        if ($requirement) {
            $requirement = ':' . $requirement;
        }

        return $this->prefix . '/' . $collection . ($one ? '/{' . $key . $requirement . '}' : '');
    }

    /**
     * Gets the URL pattern of a sub resource.
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param bool   $one
     * @param array  $options
     *
     * @return string
     */
    public function subPattern(string $parentCollection, string $subCollection, bool $one = true, array $options = [])
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
     * Generates a route for a single resource.
     *
     * @param string $method
     * @param string $collection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function one(string $method, string $collection, string $controller, array $options = [])
    {
        if (!$method) {
            throw new InvalidArgumentException('The method is required');
        }

        $singular = $options['singular'] ?? $this->singular($collection);

        if ($collection === $singular) {
            throw new InvalidArgumentException('The collection name and its singular must be different');
        }

        $method = strtolower($method);

        return $this->router->map(
            [$method],
            $this->pattern($collection, true, $options),
            $controller . ':' . $method . ucfirst($singular)
        )->setName($method . '_' . $singular);
    }

    /**
     * Generates a single route for a sub resource.
     *
     * @param string $method
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function subOne(string $method, string $parentCollection, string $subCollection, string $controller, array $options = [])
    {
        if (!$method) {
            throw new InvalidArgumentException('The method is required');
        }

        $parentSingular = $options['parent_singular'] ?? $this->singular($parentCollection);
        $subSingular = $options['sub_singular'] ?? $this->singular($subCollection);

        if ($parentCollection === $parentSingular) {
            throw new InvalidArgumentException('The parent collection name and its singular must be different');
        }

        if ($subCollection === $subSingular) {
            throw new InvalidArgumentException('The sub collection name and its singular must be different');
        }

        $method = strtolower($method);

        return $this->router->map(
            [$method],
            $this->subPattern($parentCollection, $subCollection, true, $options),
            $controller . ':' . $method . ucfirst($parentSingular) . ucfirst($subSingular)
        )->setName($method . '_' . $parentSingular . '_' . $subSingular);
    }

    /**
     * Generates a route for a collection.
     *
     * @param string $method
     * @param string $collection
     * @param string $controller
     *
     * @return RouteInterface
     */
    public function collection(string $method, string $collection, string $controller)
    {
        $method = strtolower($method);

        return $this->router->map(
            [$method],
            $this->pattern($collection, false),
            $controller . ':' . $method . ucfirst($collection)
        )->setName($method . '_' . $collection);
    }

    /**
     * Generates a route for a sub collection.
     *
     * @param string $method
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function subCollection(string $method, string $parentCollection, string $subCollection, string $controller, array $options = [])
    {
        $parentSingular = $options['parent_singular'] ?? $this->singular($parentCollection);

        return $this->router->map(
            [$method],
            $this->subPattern($parentCollection, $subCollection, false, $options),
            $controller . ':get' . ucfirst($parentSingular) . ucfirst($subCollection)
        )->setName('get_' . $parentSingular . '_' . $subCollection);
    }

    /**
     * Generates all routes for a resource.
     *
     * @param string $collection
     * @param string $controller
     * @param array  $middleware
     * @param array  $options
     *
     * @return RouteInterface[]
     */
    public function crud(string $collection, string $controller, array $middleware = [], array $options = [])
    {
        $routes = [];
        foreach ($this->CRUDMethods as $method => $enabled) {
            if ($enabled) {
                $routes[$method] = $this->$method($collection, $controller, 'post' === $method ? ($options['singular'] ?? '') : $options);
            }
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
     * Generates all routes for a sub resource.
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array  $middleware
     * @param array  $options
     *
     * @return RouteInterface[]
     */
    public function subCrud(string $parentCollection, string $subCollection, string $controller, array $middleware = [], array $options = [])
    {
        $routes = [];
        foreach ($this->CRUDMethods as $method => $enabled) {
            if ($enabled) {
                $call = $method . 'Sub';
                $routes[$method] = $this->$call($parentCollection, $subCollection, $controller, $options);
            }
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
     * Generates a route for a single resource with the GET method.
     *
     * @param string $collection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function get(string $collection, string $controller, array $options = [])
    {
        return $this->one('GET', $collection, $controller, $options);
    }

    /**
     * Generates a route for a single sub resource with the GET method.
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function getSub(string $parentCollection, string $subCollection, string $controller, array $options = [])
    {
        return $this->subOne('GET', $parentCollection, $subCollection, $controller, $options);
    }

    /**
     * Generates a route for a collection with the GET method.
     *
     * @param string $collection
     * @param string $controller
     *
     * @return RouteInterface
     */
    public function cget(string $collection, string $controller)
    {
        return $this->collection('GET', $collection, $controller);
    }

    /**
     * Generates a route for a sub resource collection with the GET method.
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function cgetSub(string $parentCollection, string $subCollection, string $controller, array $options = [])
    {
        return $this->subCollection('GET', $parentCollection, $subCollection, $controller, $options);
    }

    /**
     * Generates a route for a collection with the POST method.
     *
     * @param string $collection
     * @param string $controller
     * @param string $singular
     *
     * @return RouteInterface
     */
    public function post(string $collection, string $controller, string $singular = '')
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
     * Generates a route for a sub resource collection with the POST method.
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function postSub(string $parentCollection, string $subCollection, string $controller, array $options = [])
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
     * Generates a route for a single resource with the PUT method.
     *
     * @param string $collection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function put(string $collection, string $controller, array $options = [])
    {
        return $this->one('PUT', $collection, $controller, $options);
    }

    /**
     * Generates a route for a single sub resource with the PUT method.
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function putSub(string $parentCollection, string $subCollection, string $controller, array $options = [])
    {
        return $this->subOne('PUT', $parentCollection, $subCollection, $controller, $options);
    }

    /**
     * Generates a route for a single resource with the DELETE method.
     *
     * @param string $collection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function delete(string $collection, string $controller, array $options = [])
    {
        return $this->one('DELETE', $collection, $controller, $options);
    }

    /**
     * Generates a route for a single sub resource with the DELETE method.
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function deleteSub(string $parentCollection, string $subCollection, string $controller, array $options = [])
    {
        return $this->subOne('DELETE', $parentCollection, $subCollection, $controller, $options);
    }

    /**
     * Generates a route for a collection with the DELETE method.
     *
     * @param string $collection
     * @param string $controller
     *
     * @return RouteInterface
     */
    public function cdelete(string $collection, string $controller)
    {
        return $this->collection('DELETE', $collection, $controller);
    }

    /**
     * Generates a route for a sub resource collection with the DELETE method.
     *
     * @param string $parentCollection
     * @param string $subCollection
     * @param string $controller
     * @param array  $options
     *
     * @return RouteInterface
     */
    public function cdeleteSub(string $parentCollection, string $subCollection, string $controller, array $options = [])
    {
        return $this->subCollection('DELETE', $parentCollection, $subCollection, $controller, $options);
    }

    /**
     * Gets a resource name's singular (removes last character).
     *
     * @param string $collection
     *
     * @return string
     */
    public function singular(string $collection): string
    {
        return substr($collection, 0, -1);
    }

    /**
     * Sets URLs prefix.
     *
     * @param string $prefix
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Gets URLs prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Sets the default key.
     *
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * Gets the default key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Sets the default route placeholder requirement.
     *
     * @param string $requirement
     */
    public function setRequirement(string $requirement)
    {
        $this->requirement = $requirement;
    }

    /**
     * Gets the default route placeholder requirement.
     *
     * @return string
     */
    public function getRequirement(): string
    {
        return $this->requirement;
    }

    /**
     * Sets the CRUD methods.
     *
     * @param array $methods
     */
    public function setCRUDMethods(array $methods)
    {
        $this->CRUDMethods = $methods;
    }

    /**
     * Gets the CRUD methods.
     *
     * @return array
     */
    public function getCRUDMethods(): array
    {
        return $this->CRUDMethods;
    }
}
