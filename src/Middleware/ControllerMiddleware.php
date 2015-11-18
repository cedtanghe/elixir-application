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
use Elixir\Util\StringUtils;

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
        $package = $request->getAttribute('_package', null);
        $controller = $request->getAttribute('_controller', null);
        $action = $request->getAttribute('_action', null);
        
        if (null !== $package && null !== $controller && null !== $action)
        {
            if(false === strpos($package, '(@'))
            {
                $package = StringUtils::camelize($package);
            }
            
            $controller = sprintf('%s\Controller\%s::%s', $package, StringUtils::camelize($controller), StringUtils::camelize($action));
        }
        
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

                $controller = explode('::', $controller);
                
                if (null !== $this->locator && false !== strpos($controller[0], '(@'))
                {
                    $controller[0] = $this->locator->locateClass($controller[0]);

                    if(null === $controller[0])
                    {
                        throw new NotFoundException(sprintf('Controller "%s" was not detected.', $parts[0]));
                    }
                }
            
                $instance = $this->container->resolveClass($parts[0]);
            }
            else
            {
                $instance = $controller[0];
            }
            
            if($instance instanceof RESTfulControllerInterface)
            {
                $method = $instance->getRestFulMethodName($controller[1], $request);
            }
            else
            {
                $method = $controller[1];
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
