<?php

namespace Elixir\Test\Foundation;

use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\HTTP\ResponseFactory;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

class CreateResponseMiddleware implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response = null, callable $next = null) 
    {
        $response = ResponseFactory::createHTML('Response created.');
        return $next($request, $response);
    }
}
