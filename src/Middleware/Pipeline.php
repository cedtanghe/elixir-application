<?php

namespace Elixir\Foundation\Middleware;

use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Pipeline implements MiddlewareInterface
{
    /**
     * @var array 
     */
    protected $middleware;

    /**
     * @param array $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->middleware = $middlewares;
    }
    
    /**
     * @ignore
     */
    public function run(ServerRequestInterface $request, ResponseInterface $response = null)
    {
        return $this->__invoke($request, $response, $this);
    }
    
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response = null, callable $next = null)
    {
        if (count($this->middleware) === 0)
        {
            return $next ? $next($request, $response) : $response;
        }
        
        $middleware = array_shift($this->middleware);
        return $middleware($request, $response, count($this->middleware) > 0 ? $next : null);
    }
}
