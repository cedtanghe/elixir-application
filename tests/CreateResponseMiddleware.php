<?php

namespace Elixir\Test\Kernel;

use Elixir\HTTP\ResponseFactory;
use Elixir\Kernel\Middleware\MiddlewareInterface;

class CreateResponseMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($request, $response, callable $next) 
    {
        $response = ResponseFactory::createHTML('Response created');
        $response = $next($request, $response);
        
        $response->getBody()->write('->finalized!');
        return $response;
    }
}
