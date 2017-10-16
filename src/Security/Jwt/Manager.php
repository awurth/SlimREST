<?php

namespace App\Security\Jwt;

use App\Security\Model\AccessToken;
use App\Security\Model\RefreshToken;
use App\Security\Model\User;
use Cartalyst\Sentinel\Users\UserInterface;
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
     * Constructor
     *
     * @param string $secret
     * @param array $config
     */
    public function __construct($secret, array $config = [])
    {
        $this->secret = $secret;

        $this->accessTokenLifetime = isset($config['access_token_lifetime']) ? $config['access_token_lifetime'] : self::ACCESS_TOKEN_LIFETIME;
        $this->refreshTokenLifetime = isset($config['refresh_token_lifetime']) ? $config['refresh_token_lifetime'] : self::REFRESH_TOKEN_LIFETIME;
        $this->serverName = isset($config['server_name']) ? $config['server_name'] : '';
    }

    /**
     * Check if Access Token is valid
     *
     * @param string $token
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
     * Check if Refresh Token is valid
     *
     * @param string $token
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
     * Generate new Access Token
     *
     * @param UserInterface $user
     * @param bool $save
     * @return string
     */
    public function generateAccessToken(UserInterface $user, $save = false)
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
     * Generate new Refresh Token
     *
     * @param UserInterface $user
     * @param bool $save
     * @return string
     */
    public function generateRefreshToken(UserInterface $user, $save = false)
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
     * Get Token User
     *
     * @param string $token
     * @return User
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
     * Set server name
     *
     * @param string $name
     */
    public function setServerName($name)
    {
        $this->serverName = $name;
    }

    /**
     * Get server name
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * Set Access Token lifetime
     *
     * @param int $lifetime
     */
    public function setAccessTokenLifetime($lifetime)
    {
        $this->accessTokenLifetime = $lifetime;
    }

    /**
     * Get Access Token lifetime
     *
     * @return int
     */
    public function getAccessTokenLifetime()
    {
        return $this->accessTokenLifetime;
    }

    /**
     * Set Refresh Token lifetime
     *
     * @param int $lifetime
     */
    public function setRefreshTokenLifetime($lifetime)
    {
        $this->refreshTokenLifetime = $lifetime;
    }

    /**
     * Get Refresh Token lifetime
     *
     * @return int
     */
    public function getRefreshTokenLifetime()
    {
        return $this->refreshTokenLifetime;
    }

    /**
     * Set Access Token
     *
     * @param AccessToken $accessToken
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
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
