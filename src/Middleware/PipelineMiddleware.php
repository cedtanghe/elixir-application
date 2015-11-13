<?php

namespace Elixir\Foundation\Middleware;

use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\Foundation\Middleware\Pipeline;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class PipelineMiddleware implements MiddlewareInterface
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
}
