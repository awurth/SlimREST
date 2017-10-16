<?php

namespace App\Security\Controller;

use App\Core\Controller\Controller;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Respect\Validation\Validator as V;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends Controller
{
    public function login(Request $request, Response $response)
    {
        $credentials = [
            'username' => $request->getParam('username'),
            'password' => $request->getParam('password')
        ];

        try {
            $user = $this->sentinel->stateless($credentials);

            if ($user) {
                return $this->ok($response, [
                    'access_token' => $this->jwt->generateAccessToken($user, true),
                    'expires_in' => $this->jwt->getAccessTokenLifetime(),
                    'refresh_token' => $this->jwt->generateRefreshToken($user, true)
                ]);
            } else {
                $this->validator->addError('auth', 'Bad username or password');
            }
        } catch (ThrottlingException $e) {
            $this->validator->addError('auth', 'Too many attempts!');
        }

        return $this->validationErrors($response);
    }

    public function register(Request $request, Response $response)
    {
        $username = $request->getParam('username');
        $email = $request->getParam('email');
        $password = $request->getParam('password');

        $this->validator->request($request, [
            'username' => V::length(3, 25)->alnum('_')->noWhitespace(),
            'email' => V::noWhitespace()->email(),
            'password' => [
                'rules' => V::noWhitespace()->length(6, 25),
                'messages' => [
                    'length' => 'The password length must be between {{minValue}} and {{maxValue}} characters'
                ]
            ],
            'password_confirm' => [
                'rules' => V::equals($password),
                'messages' => [
                    'equals' => 'Passwords don\'t match'
                ]
            ]
        ]);

        if ($this->sentinel->findByCredentials(['login' => $username])) {
            $this->validator->addError('username', 'User already exists with this username.');
        }

        if ($this->sentinel->findByCredentials(['login' => $email])) {
            $this->validator->addError('email', 'User already exists with this email address.');
        }

        if ($this->validator->isValid()) {
            $role = $this->sentinel->findRoleByName('User');

            $user = $this->sentinel->registerAndActivate([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'permissions' => [
                    'user.delete' => 0
                ]
            ]);

            $role->users()->attach($user);

            return $this->created($response, 'login');
        }

        return $this->validationErrors($response);
    }

    public function refresh(Request $request, Response $response)
    {
        $this->validator->request($request, [
            'refresh_token' => [
                'rules' => V::notBlank(),
                'messages' => [
                    'notBlank' => 'Refresh token is missing'
                ]
            ]
        ]);

        $token = $request->getParam('refresh_token');

        if ($this->validator->isValid()) {
            if ($this->jwt->checkRefreshToken($token)) {
                $user = $this->jwt->getTokenUser($token);
                if ($user) {
                    return $this->ok($response, [
                        'access_token' => $this->jwt->generateAccessToken($user, true),
                        'expires_in' => $this->jwt->getAccessTokenLifetime(),
                        'refresh_token' => $this->jwt->generateRefreshToken($user, true)
                    ]);
                }

                $this->validator->addError('refresh_token', 'Unknown user');
            } else {
                $this->validator->addError('refresh_token', 'Invalid token');
            }
        }

        return $this->validationErrors($response);
    }

    public function me(Request $request, Response $response)
    {
        return $this->ok($response, $this->getUser());
    }
}
