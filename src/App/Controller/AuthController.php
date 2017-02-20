<?php

namespace App\Controller;

use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as V;

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
                    'access_token' => $this->jwt->generateToken($user, true),
                    'expires_in' => $this->jwt->getTokenLifetime()
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

        $this->validator->validate($request, [
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

    public function me(Request $request, Response $response)
    {
        return $this->ok($response, $this->getUser());
    }
}
