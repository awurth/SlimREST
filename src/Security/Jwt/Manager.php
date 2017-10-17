<?php

namespace App\Security\Jwt;

use App\Security\Model\AccessToken;
use App\Security\Model\RefreshToken;
use App\Security\Model\User;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Illuminate\Database\QueryException;

class Manager
{
    const ACCESS_TOKEN_LIFETIME = 3600;
    const REFRESH_TOKEN_LIFETIME = 1209600;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var int
     */
    private $accessTokenLifetime;

    /**
     * @var int
     */
    private $refreshTokenLifetime;

    /**
     * @var string
     */
    private $serverName;

    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * Constructor.
     *
     * @param string $secret
     * @param array  $config
     */
    public function __construct($secret, array $config = [])
    {
        $this->secret = $secret;

        $this->accessTokenLifetime = isset($config['access_token_lifetime']) ? $config['access_token_lifetime'] : self::ACCESS_TOKEN_LIFETIME;
        $this->refreshTokenLifetime = isset($config['refresh_token_lifetime']) ? $config['refresh_token_lifetime'] : self::REFRESH_TOKEN_LIFETIME;
        $this->serverName = isset($config['server_name']) ? $config['server_name'] : '';
    }

    /**
     * Checks if an Access Token is valid.
     *
     * @param string $token
     *
     * @return bool
     */
    public function checkAccessToken($token)
    {
        $accessToken = AccessToken::with('user')->where('token', $token)->first();

        if (null === $accessToken) {
            return false;
        }

        try {
            $decoded = JWT::decode($token, $this->secret, ['HS256']);

            if (!isset($decoded->exp) || $decoded->exp < time()) {
                return false;
            }
        } catch (ExpiredException $e) {
            return false;
        }

        $this->accessToken = $accessToken;

        return true;
    }

    /**
     * Checks if a Refresh Token is valid.
     *
     * @param string $token
     *
     * @return bool
     */
    public function checkRefreshToken($token)
    {
        $refreshToken = RefreshToken::where('token', $token)->first();

        if (null === $refreshToken) {
            return false;
        }

        try {
            $decoded = JWT::decode($token, $this->secret, ['HS256']);

            if (!isset($decoded->exp) || $decoded->exp < time()) {
                return false;
            }
        } catch (ExpiredException $e) {
            return false;
        }

        return true;
    }

    /**
     * Generates a new Access Token.
     *
     * @param User $user
     * @param bool $save
     *
     * @return string
     */
    public function generateAccessToken(User $user, $save = false)
    {
        $time = time();
        $expiresAt = $time + $this->accessTokenLifetime;

        $payload = [
            'iat' => $time,
            'exp' => $expiresAt,
            'data' => [
                'userId' => $user->id
            ]
        ];

        if ($this->serverName) {
            $payload['iss'] = $this->serverName;
        }

        $token = JWT::encode($payload, $this->secret);

        if ($save) {
            $accessToken = new AccessToken([
                'token' => $token,
                'expires_at' => $expiresAt
            ]);

            $accessToken->user()->associate($user);
            try {
                $accessToken->save();
            } catch (QueryException $e) {
            }
        }

        return $token;
    }

    /**
     * Generates a new Refresh Token.
     *
     * @param User $user
     * @param bool $save
     *
     * @return string
     */
    public function generateRefreshToken(User $user, $save = false)
    {
        $time = time();
        $expiresAt = $time + $this->refreshTokenLifetime;

        $payload = [
            'iat' => $time,
            'exp' => $expiresAt,
            'data' => [
                'type' => 'refresh',
                'userId' => $user->id,
                'userName' => $user->username
            ]
        ];

        if ($this->serverName) {
            $payload['iss'] = $this->serverName;
        }

        $token = JWT::encode($payload, $this->secret);

        if ($save) {
            $refreshToken = new RefreshToken([
                'token' => $token,
                'expires_at' => $expiresAt
            ]);

            $refreshToken->user()->associate($user);
            try {
                $refreshToken->save();
            } catch (QueryException $e) {
            }
        }

        return $token;
    }

    /**
     * Gets the User associated with the given Token.
     *
     * @param string $token
     *
     * @return User|null
     */
    public function getTokenUser($token) {
        try {
            $decoded = JWT::decode($token, $this->secret, ['HS256']);

            if (isset($decoded->data->userId)) {
                return User::find($decoded->data->userId);
            }
        } catch (ExpiredException $e) {
        }

        return null;
    }

    /**
     * Sets the server name.
     *
     * @param string $name
     */
    public function setServerName($name)
    {
        $this->serverName = $name;
    }

    /**
     * Gets the server name.
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * Sets the Access Token lifetime.
     *
     * @param int $lifetime
     */
    public function setAccessTokenLifetime($lifetime)
    {
        $this->accessTokenLifetime = $lifetime;
    }

    /**
     * Gets the Access Token lifetime.
     *
     * @return int
     */
    public function getAccessTokenLifetime()
    {
        return $this->accessTokenLifetime;
    }

    /**
     * Sets the Refresh Token lifetime.
     *
     * @param int $lifetime
     */
    public function setRefreshTokenLifetime($lifetime)
    {
        $this->refreshTokenLifetime = $lifetime;
    }

    /**
     * Gets the Refresh Token lifetime.
     *
     * @return int
     */
    public function getRefreshTokenLifetime()
    {
        return $this->refreshTokenLifetime;
    }

    /**
     * Sets the Access Token.
     *
     * @param AccessToken $accessToken
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Gets the Access Token.
     *
     * @return AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
