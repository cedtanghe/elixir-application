<?php

namespace Elixir\Kernel\Middleware;

use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, callable $next);
}
