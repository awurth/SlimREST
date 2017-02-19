<?php

namespace App\Service;

use App\Model\AccessToken;
use Cartalyst\Sentinel\Users\UserInterface;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class JWTManager
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var int
     */
    private $tokenLifetime;

    /**
     * @var string
     */
    private $serverName;

    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * Constructor
     *
     * @param string $secret
     * @param array $config
     */
    public function __construct($secret, array $config = [])
    {
        $this->secret = $secret;

        $this->tokenLifetime = isset($config['token_lifetime']) ? $config['token_lifetime'] : 0;
        $this->serverName = isset($config['server_name']) ? $config['server_name'] : '';
    }

    /**
     * Check if access token is valid
     *
     * @param string $token
     * @return bool
     */
    public function checkToken($token)
    {
        $accessToken = AccessToken::with('user')->where('token', $token)->first();

        if (null === $accessToken) {
            return false;
        }

        try {
            $decoded = (array) JWT::decode($token, $this->secret, ['HS256']);

            if ($decoded['exp'] < time()) {
                return false;
            }
        } catch (ExpiredException $e) {
            return false;
        }

        $this->accessToken = $accessToken;

        return true;
    }

    /**
     * Generate new JSON Web Token
     *
     * @param UserInterface $user
     * @param bool $save
     * @return string
     */
    public function generateToken(UserInterface $user, $save = false)
    {
        $time = time();

        $payload = [
            'iat' => $time,
            'data' => [
                'userId' => $user->id,
                'userName' => $user->username
            ]
        ];

        if ($this->tokenLifetime) {
            $payload['exp'] = $time + $this->tokenLifetime;
        }

        if ($this->serverName) {
            $payload['iss'] = $this->serverName;
        }

        $token = JWT::encode($payload, $this->secret);

        if ($save) {
            $accessToken = new AccessToken([
                'token' => $token
            ]);

            $accessToken->user()->associate($user);
            $accessToken->save();
        }

        return $token;
    }

    /**
     * Get token lifetime
     *
     * @return int
     */
    public function getTokenLifetime()
    {
        return $this->tokenLifetime;
    }

    /**
     * Get access token
     *
     * @return AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
}