<?php

namespace App\Controller;

use Respect\Validation\Validator as V;
use Slim\Http\Request;
use Slim\Http\Response;

class RegistrationController extends Controller
{
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
}
