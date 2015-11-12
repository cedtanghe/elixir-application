<?php

namespace Elixir\Foundation;

use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface HTTPKernelInterface 
{
    /**
     * @param MiddlewareInterface $middleware
     */
    public function pipe(MiddlewareInterface $middleware);
    
    /**
     * @return array
     */
    public function getMiddlewares();
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request);
    
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function terminate(ServerRequestInterface $request, ResponseInterface $response);
}
