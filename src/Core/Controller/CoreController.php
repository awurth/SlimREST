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
                'oauth_token' => $this->path('oauth_token'),
                'register' => $this->path('register'),
                'user' => $this->path('users.me')
            ]
        ]);
    }
}
