<?php

namespace Elixir\Foundation\Middleware;

use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\Foundation\Middleware\Pipeline;
use Elixir\Foundation\Middleware\TerminableInterface;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class PipelineMiddleware implements MiddlewareInterface, TerminableInterface
{
    /**
     * @var Pipeline 
     */
    protected $pipeline;

    /**
     * @param Pipeline $pipeline
     */
    public function __construct(Pipeline $pipeline)
    {
        $this->pipeline = $pipeline;
    }
    
    /**
     * {@inheritdoc}
     */
    public function __invoke($request, $response, callable $next) 
    {
        $response = $this->pipeline->process($request, $response);
        return $next($request, $response);
    }
    
    /**
     * {@inheritdoc}
     */
    public function terminate(ServerRequestInterface $request, ResponseInterface $response)
    {
        $middlewares = array_reverse($this->pipeline->getMiddlewares());
        
        foreach ($middlewares as $middleware)
        {
            if ($middleware instanceof TerminableInterface)
            {
                $middleware->terminate($request, $response);
            }
        }
    }
}
