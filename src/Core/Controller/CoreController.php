<?php

namespace App\Core\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class CoreController extends Controller
{
    public function root(Request $request, Response $response)
    {
        return $this->ok($response, [
            'security' => [
                'login' => $this->path('login'),
                'register' => $this->path('register'),
                'refresh_token' => $this->path('jwt.refresh'),
                'user' => $this->path('users.me')
            ]
        ]);
    }
}
