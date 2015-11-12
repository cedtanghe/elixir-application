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
     * @var callable
     */
    protected $done;

    /**
     * @param array $middlewares
     * @param \Elixir\HTTP\callable $done
     */
    public function __construct(array $middlewares, callable $done)
    {
        $this->middleware = $middlewares;
        $this->done = $done;
    }
    
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response = null, callable $next = null)
    {
        if (count($this->middleware) === 0)
        {
            $done = $this->done;
            return $done($request, $response, $next);
        }
        
        $middleware = array_shift($this->middleware);
        return $middleware->handle($request, $response, $this);
    }
}
