<?php

namespace Elixir\Kernel\Middleware;

use Elixir\DI\ContainerAwareInterface;
use Elixir\DI\ContainerInterface;
use Elixir\DI\ContainerResolvableInterface;
use Elixir\Kernel\Controller\RESTfulControllerInterface;
use Elixir\Kernel\LocatorAwareInterface;
use Elixir\Kernel\LocatorInterface;
use Elixir\Kernel\Middleware\MiddlewareInterface;
use function Elixir\STDLib\camelize;

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
     * @throws LogicException
     */
    public function setContainer(ContainerInterface $container = null)
    {
        if (!$container instanceof ContainerResolvableInterface)
        {
            throw new LogicException('Container must implement the interface "\Elixir\DI\ContainerResolvableInterface".');
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
     * @throws \RuntimeException
     */
    public function __invoke($request, $response, callable $next) 
    {
        $module = $request->getAttribute('module', null);
        $controller = $request->getAttribute('controller', null);
        $action = $request->getAttribute('action', null);
        
        if (null !== $module && null !== $controller && null !== $action)
        {
            if(false === strpos($module, '(@'))
            {
                $module = camelize($module);
            }
            
            $controller = sprintf('%s\Controller\%s::%s', $module, camelize($controller), camelize($action));
        }
        
        if (empty($controller))
        {
            throw new \RuntimeException('No controller found.');
        }
        
        if (is_array($controller))
        {
            $instance = $controller[0];
        }
        else if (false !== strpos($controller, '::'))
        {
            $controller = explode('::', $controller);

            if (null !== $this->locator && false !== strpos($controller[0], '(@'))
            {
                $controller[0] = $this->locator->locateClass($controller[0]);

                if(null === $controller[0])
                {
                    throw new \RuntimeException(sprintf('Controller "%s" was not detected.', $parts[0]));
                }
            }

            $instance = $this->container->resolveClass($parts[0]);
        }
        
        if (isset($instance))
        {
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
                'resolver_arguments_available' => $request->getAttributes() + ['request' => $request, 'response' => $response],
                'resolve' => true
            ]
        );
        
        $response = call_user_func_array($callable[0], $callable[1]);
        return $next($request, $response);
    }
}
