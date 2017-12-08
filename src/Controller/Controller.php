<?php

namespace App\Controller;

use App\Exception\AccessDeniedException;
use App\Exception\UnauthorizedException;
use Awurth\SlimValidation\Validator;
use Cartalyst\Sentinel\Sentinel;
use OAuth2\Server;
use Psr\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * @property Server    oauth
 * @property Router    router
 * @property Sentinel  sentinel
 * @property Validator validator
 */
abstract class Controller
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Throws an AccessDeniedException if user doesn't have the required role.
     *
     * @param string $role
     *
     * @throws AccessDeniedException
     */
    protected function requireRole($role)
    {
        $user = $this->sentinel->getUser();

        if (null === $user) {
            throw $this->unauthorizedException();
        }

        if (!$user->inRole($role)) {
            throw $this->accessDeniedException('Access denied: User must have role '.$role);
        }
    }

    /**
     * Gets request parameters.
     *
     * @param Request  $request
     * @param string[] $params
     * @param mixed    $default
     *
     * @return array
     */
    protected function params(Request $request, array $params, $default = null)
    {
        $data = [];
        foreach ($params as $param) {
            $data[$param] = $request->getParam($param, $default);
        }

        return $data;
    }

    /**
     * Generates a URL from a route.
     *
     * @param string $route
     * @param array  $params
     * @param array  $queryParams
     *
     * @return string
     */
    protected function path($route, array $params = [], array $queryParams = [])
    {
        return $this->router->pathFor($route, $params, $queryParams);
    }

    /**
     * Generates a relative URL from a route.
     *
     * @param string $route
     * @param array  $params
     * @param array  $queryParams
     *
     * @return string
     */
    protected function relativePath($route, array $params = [], array $queryParams = [])
    {
        return $this->router->relativePathFor($route, $params, $queryParams);
    }

    /**
     * Redirects to a route.
     *
     * @param Response $response
     * @param string   $route
     * @param array    $params
     *
     * @return Response
     */
    protected function redirect(Response $response, $route, array $params = [])
    {
        return $response->withRedirect($this->router->pathFor($route, $params));
    }

    /**
     * Redirects to a url.
     *
     * @param Response $response
     * @param string   $url
     *
     * @return Response
     */
    protected function redirectTo(Response $response, $url)
    {
        return $response->withRedirect($url);
    }

    /**
     * Returns a "200 Ok" response with JSON data.
     *
     * @param Response $response
     * @param mixed    $data
     *
     * @return Response
     */
    protected function ok(Response $response, $data)
    {
        return $this->json($response, $data);
    }

    /**
     * Returns a "201 Created" response with a location header.
     *
     * @param Response $response
     * @param string   $route
     * @param array    $params
     *
     * @return Response
     */
    protected function created(Response $response, $route, array $params = [])
    {
        return $this->redirect($response, $route, $params)->withStatus(201);
    }

    /**
     * Returns a "204 No Content" response.
     *
     * @param Response $response
     *
     * @return Response
     */
    protected function noContent(Response $response)
    {
        return $response->withStatus(204);
    }

    /**
     * Returns validation errors as a JSON array.
     *
     * @param Response $response
     *
     * @return Response
     */
    protected function validationErrors(Response $response)
    {
        return $this->json($response, $this->validator->getErrors(), 400);
    }

    /**
     * Writes JSON in the response body.
     *
     * @param Response $response
     * @param mixed    $data
     * @param int      $status
     *
     * @return Response
     */
    protected function json(Response $response, $data, $status = 200)
    {
        return $response->withJson($data, $status);
    }

    /**
     * Writes text in the response body.
     *
     * @param Response $response
     * @param string   $data
     * @param int      $status
     *
     * @return int
     */
    protected function write(Response $response, $data, $status = 200)
    {
        return $response->withStatus($status)->getBody()->write($data);
    }

    /**
     * Creates a new NotFoundException.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return NotFoundException
     */
    protected function notFoundException(Request $request, Response $response)
    {
        return new NotFoundException($request, $response);
    }

    /**
     * Creates a new UnauthorizedException.
     *
     * @param string $message
     *
     * @return UnauthorizedException
     */
    protected function unauthorizedException($message = 'Unauthorized.')
    {
        return new UnauthorizedException($message);
    }

    /**
     * Creates a new AccessDeniedException.
     *
     * @param string $message
     *
     * @return AccessDeniedException
     */
    protected function accessDeniedException($message = 'Access Denied.')
    {
        return new AccessDeniedException($message);
    }

    /**
     * Gets a service from the container.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->container->get($property);
    }
}
