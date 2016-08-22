<?php

namespace Elixir\Kernel\Middleware;

use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface TerminableInterface extends MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     */
    public function terminate(ServerRequestInterface $request, ResponseInterface $response);
}
