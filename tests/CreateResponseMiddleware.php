<?php

namespace Elixir\Test\Foundation;

use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\HTTP\ResponseFactory;

class CreateResponseMiddleware implements MiddlewareInterface
{
    public function __invoke($request, $response, callable $next) 
    {
        $response = ResponseFactory::createHTML('Response created.');
        $response = $next($request, $response);
        
        $response->getBody()->write('Finalized!');
        return $response;
    }
}
