<?php

namespace Elixir\Foundation\Middleware;

use Elixir\DI\ContainerAwareInterface;
use Elixir\DI\ContainerInterface;
use Elixir\DI\ContainerResolvableInterface;
use Elixir\Foundation\Controller\RESTfulControllerInterface;
use Elixir\Foundation\Exception\NotFoundException;
use Elixir\Foundation\LocatorAwareInterface;
use Elixir\Foundation\LocatorInterface;
use Elixir\Foundation\Middleware\MiddlewareInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ControllerMiddleware implements MiddlewareInterface, ContainerAwareInterface, LocatorAwareInterface
{
    /**
     * @var LocatorInterface
     */
    protected $locator;
    
    /**
     * @var ContainerResolvableInterface
     */
    protected $container;
    
    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function setContainer(ContainerInterface $container = null)
    {
        if (!$container instanceof ContainerResolvableInterface)
        {
            throw new \LogicException('Container must implement the interface "\Elixir\DI\ContainerResolvableInterface".');
        }
        
        $this->container = $container;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setLocator(LocatorInterface $locator = null)
    {
        $this->locator = $locator;
    }
    
    /**
     * {@inheritdoc}
     * @throws NotFoundException
     * @throws \LogicException
     */
    public function __invoke($request, $response, callable $next) 
    {
        $controller = $request->getAttribute('_controller', null);
        
        if (empty($controller))
        {
            throw new NotFoundException('No controller found.');
        }
        
        if (is_string($controller) || is_array($controller))
        {
            if (is_string($controller))
            {
                if (false === strpos($controller, '::'))
                {
                    throw new \LogicException(sprintf('Controller "%s" is not callable.', $controller));
                }

                $parts = explode('::', $controller);
            }
            else
            {
                $parts = $controller;
            }
            
            if (is_string($parts[0]))
            {
                if (null !== $this->locator && false !== strpos($parts[0], '(@'))
                {
                    $parts[0] = $this->locator->locateClass($parts[0]);

                    if(null === $parts[0])
                    {
                        throw new NotFoundException(sprintf('Controller "%s" was not detected.', $parts[0]));
                    }
                }
            
                $instance = $this->container->resolveClass($parts[0]);
            }
            else
            {
                $instance = $parts[0];
            }
            
            if($instance instanceof RESTfulControllerInterface)
            {
                $method = $instance->getRestFulMethodName($parts[1], $request);
            }
            else
            {
                $method = $parts[1];
            }
            
            $controller = [$instance, $method];
        }
        
        $callable = $this->container->resolve(
            $controller, 
            [
                'resolver_arguments_available' => $request->getAttributes() + ['request' => $request, 'response' => $response]
            ]
        );
        
        $response = call_user_func_array($callable[0], $callable[1]);
        return $next($request, $response);
    }
}
