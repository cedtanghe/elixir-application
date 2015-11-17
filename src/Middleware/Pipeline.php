<?php

namespace Elixir\Foundation\Middleware;

use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Pipeline
{
    /**
     * @var array 
     */
    protected $middlewares;

    /**
     * @param array $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }
    
    /**
     * @return array
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response = null)
    {
        if (count($this->middlewares) === 0)
        {
            return $response;
        }
        
        $middleware = array_shift($this->middlewares);
        
        return $middleware($request, $response, function($request, $response)
        {
            return $this->process($request, $response);
        });
    }
}
