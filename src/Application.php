<?php

namespace Elixir\Foundation;

use Elixir\DI\ContainerInterface;
use Elixir\Dispatcher\DispatcherInterface;
use Elixir\Dispatcher\DispatcherTrait;
use Elixir\Foundation\ApplicationEvent;
use Elixir\Foundation\ApplicationInterface;
use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\Foundation\Middleware\Pipeline;
use Elixir\Foundation\Middleware\TerminableInterface;
use Elixir\Foundation\Package\PackageInterface;
use Elixir\HTTP\ResponseFactory;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Application implements ApplicationInterface, DispatcherInterface
{
    use DispatcherTrait;
    
    /**
     * @var array
     */
    protected $middlewares = [];
    
    /**
     * @var array
     */
    protected $packages = [];
    
    /**
     * @var ContainerInterface 
     */
    protected $container;
    
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->container->instance('Elixir\Foundation\ApplicationInterface', $this, ['aliases' => 'application']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * return boolean
     */
    public function hasMiddleware(MiddlewareInterface $middleware)
    {
        return in_array($middleware, $this->middlewares, true);
    }
    
    /**
     * {@inheritdoc}
     */
    public function pipe(MiddlewareInterface $middleware)
    {
        $this->middlewares[] = $middleware;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }
    
    /**
     * return boolean
     */
    public function hasPackage($name)
    {
        foreach ($this->packages as $package)
        {
            if ($package->getName())
            {
                return true;
            }
        }
        
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function register(PackageInterface $package);
    
    /**
     * {@inheritdoc}
     */
    public function getPackage($name)
    {
        foreach ($this->packages as $package)
        {
            if ($package->getName())
            {
                return $package;
            }
        }
        
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPackages()
    {
        return $this->packages;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getHierarchy($packageName);
    
    /**
     * {@inheritdoc}
     */
    public function locateClass($className);

    /**
     * {@inheritdoc}
     */
    public function locateFile($filePath, $single = true);
    
    /**
     * {@inheritdoc}
     */
    public function isBooted();
    
    /**
     * {@inheritdoc}
     */
    public function boot();
    
    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function handle(ServerRequestInterface $request)
    {
        if ($request->isMainRequest())
        {
            $this->container->instance('Elixir\HTTP\ServerRequestInterface', $request, ['aliases' => 'request']);
        }
        
        $event = new ApplicationEvent(ApplicationEvent::REQUEST, ['request' => $request]);
        $this->dispatch($event);
        
        $request = $event->getRequest();
        
        $pipeline = new Pipeline($this->middlewares);
        $response = $pipeline->process($request);
        
        if (is_string($response))
        {
            $response = ResponseFactory::createHTML($response, 200);
        }
        
        $event = new ApplicationEvent(ApplicationEvent::RESPONSE, ['response' => $response]);
        $this->dispatch($event);
        
        $response = $event->getResponse();
        
        if (null === $response)
        {
            throw new \LogicException('No response found.');
        }
        
        return $response;
    }
    
    /**
     * {@inheritdoc}
     */
    public function terminate(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->dispatch(new ApplicationEvent(ApplicationEvent::TERMINATE, ['request' => $request, 'response' => $response]));
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
