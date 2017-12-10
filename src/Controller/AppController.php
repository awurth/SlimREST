<?php

namespace App\Controller;

use Awurth\Slim\Helper\Controller\RestController;
use Slim\Http\Request;
use Slim\Http\Response;

class AppController extends RestController
{
    public function root(Request $request, Response $response)
    {
        return $this->ok($response, [
            'security' => [
                'oauth_token' => $this->relativePath('oauth_token'),
                'register'    => $this->relativePath('register'),
                'user'        => $this->relativePath('user')
            ]
        ]);
    }
}
