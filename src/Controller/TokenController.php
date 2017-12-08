<?php

namespace App\Controller;

use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;
use Slim\Http\Request;
use Slim\Http\Response;

class TokenController extends Controller
{
    public function token(Request $request)
    {
        $oauthRequest = RequestBridge::toOAuth2($request);

        $oauthResponse = $this->oauth->handleTokenRequest($oauthRequest);

        return ResponseBridge::fromOauth2($oauthResponse);
    }

    public function user(Request $request, Response $response)
    {
        return $this->ok($response, $this->sentinel->getUser());
    }
}
