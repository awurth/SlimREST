<?php

namespace App\Core\Controller;

use App\Security\Exception\AccessDeniedException;
use App\Security\Exception\UnauthorizedException;
use App\Security\Model\User;
use App\Security\Jwt\Manager as JwtManager;
use Awurth\SlimValidation\Validator;
use Cartalyst\Sentinel\Sentinel;
use Psr\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * @property Router     router
 * @property Validator  validator
 * @property Sentinel   sentinel
 * @property JwtManager jwt
 * @property string     env
 * @property string     root_dir
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
     * Gets the current authenticated user.
     *
     * @return User|null
     */
    public function getUser()
    {
        $token = $this->jwt->getAccessToken();

        return $token ? $token->user : null;
    }

    /**
     * Throws an AccessDeniedException if user doesn't have the required role.
     *
     * @param string $role
     *
     * @throws AccessDeniedException
     */
    public function requireRole($role)
    {
        $user = $this->getUser();

        if (null === $user || !$user->inRole($role)) {
            throw $this->accessDeniedException('Access denied: User must have role ' . $role);
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
    public function params(Request $request, array $params, $default = null)
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
     *
     * @return string
     */
    public function path($route, array $params = [])
    {
        return $this->router->pathFor($route, $params);
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
    public function redirect(Response $response, $route, array $params = [])
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
    public function redirectTo(Response $response, $url)
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
    public function ok(Response $response, $data)
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
    public function created(Response $response, $route, array $params = [])
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
    public function noContent(Response $response)
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
    public function validationErrors(Response $response)
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
    public function json(Response $response, $data, $status = 200)
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
    public function write(Response $response, $data, $status = 200)
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
    public function notFoundException(Request $request, Response $response)
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
    public function unauthorizedException($message = 'Unauthorized.')
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
    public function accessDeniedException($message = 'Access Denied.')
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
