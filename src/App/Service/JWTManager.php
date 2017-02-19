<?php

namespace App\Service;

use App\Model\AccessToken;
use Cartalyst\Sentinel\Users\UserInterface;
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
}