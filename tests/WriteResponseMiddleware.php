<?php

namespace Elixir\Test\Foundation;

use Elixir\Foundation\Middleware\MiddlewareInterface;

class WriteResponseMiddleware implements MiddlewareInterface
{
    public function __invoke($request, $response, callable $next) 
    {
        $response->getBody()->write('Write.');
        return $next($request, $response);
    }
}
