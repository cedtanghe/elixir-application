<?php

namespace Elixir\Kernel\Middleware;

use Elixir\DI\ContainerAwareInterface;
use Elixir\DI\ContainerInterface;
use Elixir\HTTP\ResponseFactory;
use Elixir\HTTP\ResponseInterface;
use Elixir\Kernel\HTTPKernelEvent;
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
        $this->failbackErrorController = $failbackErrorController;
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
            
            if (is_callable($this->failbackErrorController))
            {
                return call_user_func_array($this->failbackErrorController, $request, $e);
            }
            else
            {
                $event = new HTTPKernelEvent(HTTPKernelEvent::EXCEPTION, ['request' => $request, 'exception' => $e]);
                $this->kernel->dispatch($event);

                $request = $event->getRequest();
                $response = $event->getResponse();

                if ($response instanceof ResponseInterface)
                {
                    $response = $response->withStatus($statusCode);
                    return $response;
                }
                
                if ($request->isMainRequest())
                {
                    if ($request->hasAttribute('module'))
                    {
                        $module = $request->getAttribute('module');
                        $controller = 'error';
                        $action = $request->getAttribute('action');
                        
                        $request = $request->withoutAttributes();
                        $request = $request->withAttribute('exception', $e);
                        $request = $request->withAttribute('module', $module);
                        $request = $request->withAttribute('controller', $controller);
                        $request = $request->withAttribute('action', $action);
                        
                        $request->setParentRequest($request);

                        $response = $this->kernel->handle($request);
                        $response = $response->withStatus($statusCode);
                    }
                    else
                    {
                        $response = ResponseFactory::create($message, $statusCode);
                    }
                }
                else
                {
                    $response = ResponseFactory::create($message, $statusCode);
                }
            }
        }
        
        return $response;
    }
}
