<?php

namespace App\Middleware;

use App\Exception\UnauthorizedException;
use App\Model\User;
use Cartalyst\Sentinel\Sentinel;
use Chadicus\Slim\OAuth2\Http\RequestBridge;
use OAuth2\Server;
use Slim\Http\Request;
use Slim\Http\Response;

class Authorization implements MiddlewareInterface
{
    /**
     * @var array
     */
    protected $scopes;

    /**
     * @var Sentinel
     */
    protected $sentinel;

    /**
     * @var Server;
     */
    protected $server;

    /**
     * Constructor.
     *
     * @param Server   $server
     * @param Sentinel $sentinel
     * @param array    $scopes
     */
    public function __construct(Server $server, Sentinel $sentinel, array $scopes = [])
    {
        $this->server = $server;
        $this->sentinel = $sentinel;
        $this->scopes = $this->formatScopes($scopes);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $oauth2Request = RequestBridge::toOAuth2($request);
        foreach ($this->scopes as $scope) {
            if ($this->server->verifyResourceRequest($oauth2Request, null, $scope)) {
                $token = $this->server->getResourceController()->getToken();
                $user = User::find($token['user_id']);

                $this->sentinel->stateless($user);

                return $next($request, $response);
            }
        }

        throw new UnauthorizedException();
    }

    /**
     * Returns a callable function to be used as a authorization middleware with a specified scope.
     *
     * @param array $scopes Scopes require for authorization.
     *
     * @return Authorization
     */
    public function withRequiredScope(array $scopes)
    {
        $clone = clone $this;
        $clone->scopes = $clone->formatScopes($scopes);

        return $clone;
    }

    /**
     * Helper method to ensure given scopes are formatted properly.
     *
     * @param array $scopes Scopes required for authorization.
     *
     * @return array The formatted scopes array.
     */
    protected function formatScopes(array $scopes)
    {
        if (empty($scopes)) {
            return [null];
        }

        array_walk($scopes, function (&$scope) {
            if (is_array($scope)) {
                $scope = implode(' ', $scope);
            }
        });

        return $scopes;
    }
}
