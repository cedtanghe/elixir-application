<?php

namespace Elixir\Foundation\Middleware;

use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface MiddlewareInterface 
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response = null, callable $next = null);
}
