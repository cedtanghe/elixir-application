<?php

namespace Elixir\Kernel;

use Elixir\Dispatcher\Event;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;
use Elixir\Kernel\Middleware\MiddlewareInterface;
use Elixir\Kernel\Module\ModuleInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ApplicationEvent extends Event 
{
    /**
     * @var string
     */
    const MODULE_ADDED = 'module_added';
    
    /**
     * @var string
     */
    const MIDDLEWARE_ADDED = 'middleware_added';
    
    /**
     * @var string
     */
    const BOOTED = 'booted';
    
    /**
     * @var string
     */
    const REQUEST = 'request';
    
    /**
     * @var string
     */
    const EXCEPTION = 'exception';
    
    /**
     * @var string
     */
    const RESPONSE = 'response';
    
    /**
     * @var string
     */
    const TERMINATE = 'terminate';
    
    /**
     * @var ModuleInterface
     */
    protected $module;
    
    /**
     * @var MiddlewareInterface
     */
    protected $middleware;
    
    /**
     * @var ServerRequestInterface
     */
    protected $request;
    
    /**
     * @var \Exception
     */
    protected $exception;
    
    /**
     * @var ResponseInterface
     */
    protected $response;
    
    /**
     * {@inheritdoc}
     * @param array $params
     */
    public function __construct($pType, array $params = [])
    {
        parent::__construct($pType);
        
        $params += [
            'module' => null,
            'middleware' => null,
            'request' => null,
            'exception' => null,
            'response' => null,
        ];
        
        $this->module = $params['module'];
        $this->middleware = $params['middleware'];
        $this->request = $params['request'];
        $this->exception = $params['exception'];
        $this->response = $params['response'];
    }

    /**
     * @return ModuleInterface
     */
    public function getModule()
    {
        return $this->module;
    }
    
    /**
     * @return MiddlewareInterface
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }
    
    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
    
    /**
     * @param ServerRequestInterface $request
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
    
    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
