<?php

namespace Elixir\Foundation\Middleware;

use Elixir\Foundation\Middleware\MiddlewareInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ErrorMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($request, $response, callable $next) 
    {
        // Todo
        return $next($request, $response);
    }
}
