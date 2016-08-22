<?php

namespace Elixir\Test\Kernel;

use Elixir\Kernel\Middleware\MiddlewareInterface;

class WriteResponseMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($request, $response, callable $next)
    {
        $response->getBody()->write('->write');

        return $next($request, $response);
    }
}
