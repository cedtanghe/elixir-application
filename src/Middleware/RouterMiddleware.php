<?php

namespace Elixir\Kernel\Middleware;

use Elixir\DI\ContainerAwareInterface;
use Elixir\DI\ContainerInterface;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;
use Elixir\Kernel\HTTPKernelInterface;
use Elixir\Kernel\LocatorAwareInterface;
use Elixir\Kernel\LocatorInterface;
use Elixir\Kernel\Middleware\MiddlewareInterface;
use Elixir\Kernel\Middleware\TerminableInterface;
use Elixir\Routing\RouterInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class RouterMiddleware implements MiddlewareInterface, ContainerAwareInterface, TerminableInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @var array 
     */
    protected $middlewares = [];
    
    /**
     * @var RouterInterface
     */
    protected $router;
    
    /**
     * @var HTTPKernelInterface
     */
    protected $kernel;
    
    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->router = $container->get('Elixir\Routing\RouterInterface');
        $this->kernel = $container->get('kernel');
    }
    
    /**
     * {@inheritdoc}
     */
    public function __invoke($request, $response, callable $next) 
    {
        if ($request->isMainRequest())
        {
            $routeMatch = $this->router->match(trim($request->getPathInfo(), '/'));
            $request = $request->withAttributes($routeMatch->all() + $request->getAttributes());
            
            if ($routeMatch->has('middlewares'))
            {
                $this->middlewares = $routeMatch->get('middlewares');
                $kernelIsLocator = $this->kernel instanceof LocatorInterface;
                
                foreach ($this->middlewares as $middleware)
                {
                    if ($middleware instanceof ContainerAwareInterface)
                    {
                        $middleware->setContainer($this->container);
                    }
                    
                    if ($kernelIsLocator && $middleware instanceof LocatorAwareInterface)
                    {
                        $middleware->setLocator($this->kernel);
                    }
                }
                
                $pipelineMiddleware = new PipelineMiddleware(new Pipeline($middlewares));
                
                return $pipelineMiddleware($request, $response, function($request, $response) use ($next)
                {
                    return $next($request, $response);
                });
            }
        }
        
        return $next($request, $response);
    }
    
    /**
     * {@inheritdoc}
     */
    public function terminate(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($request->isMainRequest() && count($this->middlewares) > 0)
        {
            $middlewares = array_reverse($this->middlewares);

            foreach ($middlewares as $middleware)
            {
                if ($middleware instanceof TerminableInterface)
                {
                    $middleware->terminate($request, $response);
                }
            }
        }
    }
}
