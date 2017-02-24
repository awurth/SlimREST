<?php

namespace App\Controller;

use App\Exception\AccessDeniedException;
use App\Model\User;
use App\Service\JWTManager;
use Awurth\Slim\Rest\Validation\Validator;
use Cartalyst\Sentinel\Sentinel;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use Slim\Router;

/**
 * @property Router router
 * @property Validator validator
 * @property Sentinel sentinel
 * @property JWTManager jwt
 */
abstract class Controller
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

    /**
     * Get current authenticated user
     *
     * @return User|null
     */
    public function getUser()
    {
        $token = $this->jwt->getAccessToken();

        return $token ? $token->user : null;
    }

    /**
     * Throw an AccessDeniedException if user doesn't have the required role
     *
     * @param string $role
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
     * Stop the script and print info about a variable
     *
     * @param mixed $data
     */
    public function debug($data)
    {
        die('<pre>' . print_r($data, true) . '</pre>');
    }

    /**
     * Get request params
     *
     * @param Request $request
     * @param string[] $params
     * @return array
     */
    public function params(Request $request, array $params)
    {
        $data = [];
        foreach ($params as $param) {
            $data[$param] = $request->getParam($param);
        }

        return $data;
    }

    /**
     * Redirect to route
     *
     * @param Response $response
     * @param string $route
     * @param array $params
     * @return Response
     */
    public function redirect(Response $response, $route, array $params = [])
    {
        return $response->withRedirect($this->router->pathFor($route, $params));
    }

    /**
     * Redirect to url
     *
     * @param Response $response
     * @param string $url
     *
     * @return Response
     */
    public function redirectTo(Response $response, $url)
    {
        return $response->withRedirect($url);
    }

    /**
     * Return "200 Ok" response with JSON data
     *
     * @param Response $response
     * @param mixed $data
     * @return int
     */
    public function ok(Response $response, $data)
    {
        return $this->json($response, $data);
    }

    /**
     * Return "201 Created" response with location header
     *
     * @param Response $response
     * @param string $route
     * @param array $params
     * @return Response
     */
    public function created(Response $response, $route, array $params = [])
    {
        return $this->redirect($response, $route, $params)->withStatus(201);
    }

    /**
     * Return "204 No Content" response
     *
     * @param Response $response
     * @return Response
     */
    public function noContent(Response $response)
    {
        return $response->withStatus(204);
    }

    /**
     * Return validation errors as a JSON array
     *
     * @param Response $response
     * @return int
     */
    public function validationErrors(Response $response)
    {
        return $this->json($response, $this->validator->getErrors(), 400);
    }

    /**
     * Write JSON in the response body
     *
     * @param Response $response
     * @param mixed $data
     * @param int $status
     * @return int
     */
    public function json(Response $response, $data, $status = 200)
    {
        return $response->withJson($data, $status);
    }

    /**
     * Write text in the response body
     *
     * @param Response $response
     * @param string $data
     * @param int $status
     * @return int
     */
    public function write(Response $response, $data, $status = 200)
    {
        return $response->withStatus($status)->getBody()->write($data);
    }

    /**
     * Create new NotFoundException
     *
     * @param Request $request
     * @param Response $response
     * @return NotFoundException
     */
    public function notFoundException(Request $request, Response $response)
    {
        return new NotFoundException($request, $response);
    }

    /**
     * Create new AccessDeniedException
     *
     * @param string $message
     * @return AccessDeniedException
     */
    public function accessDeniedException($message = "Access denied")
    {
        return new AccessDeniedException($message);
    }

    public function __get($property)
    {
        return $this->container->get($property);
    }
}
