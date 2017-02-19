<?php

namespace App\Middleware;

class GuestMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if ($this->sentinel->check()) {
            return $response->withRedirect($this->router->pathFor('home'));
        }

        return $next($request, $response);
    }
}
