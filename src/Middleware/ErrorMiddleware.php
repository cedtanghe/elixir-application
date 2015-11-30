<?php

namespace Elixir\Kernel\Middleware;

use Elixir\DI\ContainerAwareInterface;
use Elixir\DI\ContainerInterface;
use Elixir\HTTP\ResponseFactory;
use Elixir\HTTP\ResponseInterface;
use Elixir\Kernel\ApplicationEvent;
use Elixir\Kernel\ApplicationInterface;
use Elixir\Kernel\HTTPKernelInterface;
use Elixir\Kernel\Middleware\MiddlewareInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ErrorMiddleware implements MiddlewareInterface, ContainerAwareInterface
{
    /**
     * @var string|array|callable
     */
    protected $failbackErrorController;
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @var HTTPKernelInterface
     */
    protected $kernel;
    
    /**
     * @param string|array|callable $failbackErrorController
     */
    public function __construct($failbackErrorController = null) 
    {
        $this->failbackErrorController = null;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->kernel = $container->get('kernel');
    }
    
    /**
     * {@inheritdoc}
     */
    public function __invoke($request, $response, callable $next) 
    {
        try
        {
            $response = $next($request, $response);
        }
        catch(\Exception $e)
        {
            switch($e->getCode())
            {
                case 403:
                case 404:
                case 500:
                    $statusCode = $e->getCode();
                    $message = $e->getMessage();
                    break;
                default:
                    $statusCode = 500;
                    $message = 'Internal Server Error';
            }
            
            if ($this->kernel instanceof ApplicationInterface)
            {
                $event = new ApplicationEvent(ApplicationEvent::EXCEPTION, ['request' => $request, 'response' => $response, 'exception' => $e]);
                $this->kernel->dispatch($event);
                
                $response = $event->getRequest();
                $response = $event->getResponse();
                
                if ($response instanceof ResponseInterface)
                {
                    $response = $response->withStatus($statusCode);
                    return $response;
                }
            }
            
            if ($request->isMainRequest())
            {
                $controller = $this->failbackErrorController;
                
                if ($request->hasAttribute('_module'))
                {
                    $controller = 'error';
                }
                
                if (empty($controller))
                {
                    $response = ResponseFactory::create($message, $statusCode);
                    return;
                }
                
                $request = $request->withoutAttributes();
                $request = $request->setParentRequest($request);
                
                if (is_string($controller))
                {
                    $parts = explode('::', $controller);
                    
                    if (count($parts) === 3)
                    {
                        $request = $request->withAttribute('_module', $parts[0]);
                        $request = $request->withAttribute('_controller', $parts[1]);
                        $request = $request->withAttribute('_action', $parts[2]);
                    }
                    else
                    {
                        $request = $request->withAttribute('_controller', $controller);
                    }
                }
                else
                {
                    $request = $request->withAttribute('_controller', $controller);
                }
                
                $response = $this->kernel->handle($request);
                $response = $response->withStatus($statusCode);
            }
            else
            {
                $response = ResponseFactory::create($message, $statusCode);
            }
        }
        
        return $response;
    }
}
