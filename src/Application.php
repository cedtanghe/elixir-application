<?php

namespace Elixir\Foundation;

use Elixir\Dispatcher\DispatcherInterface;
use Elixir\Dispatcher\DispatcherTrait;
use Elixir\Foundation\ApplicationEvent;
use Elixir\Foundation\ApplicationInterface;
use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\Foundation\Middleware\Pipeline;
use Elixir\Foundation\Middleware\TerminableInterface;
use Elixir\Foundation\Package\PackageInterface;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Application implements ApplicationInterface, DispatcherInterface
{
    use DispatcherTrait;
    
    protected $middlewares = [];
    
    /**
     * {@inheritdoc}
     */
    public function getContainer();
    
    /**
     * {@inheritdoc}
     */
    public function pipe(MiddlewareInterface $middleware);
    
    /**
     * {@inheritdoc}
     */
    public function getMiddlewares();

    /**
     * {@inheritdoc}
     */
    public function register(PackageInterface $package);
    
    /**
     * {@inheritdoc}
     */
    public function getPackage($name);
    
    /**
     * {@inheritdoc}
     */
    public function getPackages();
    
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
     */
    public function handle(ServerRequestInterface $request)
    {
        $this->dispatch(new ApplicationEvent(ApplicationEvent::HANDLE, ['request' => $request]));
        
        $pipeline = new Pipeline($this->middlewares);
        $response = $pipeline->process($request);
        
        // Todo parse response.
        
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
