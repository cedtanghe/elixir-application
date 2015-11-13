<?php

namespace Elixir\Test\Foundation;

use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

class WriteResponseMiddleware implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response = null, callable $next = null) 
    {
        $response->getBody()->write('Write.');
        return $next($request, $response);
    }
}
