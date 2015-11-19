<?php

namespace Elixir\Kernel;

use Elixir\Dispatcher\Event;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;
use Elixir\Kernel\Middleware\MiddlewareInterface;
use Elixir\Kernel\Package\PackageInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ApplicationEvent extends Event 
{
    /**
     * @var string
     */
    const PACKAGE_ADDED = 'package_added';
    
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
     * @var PackageInterface
     */
    protected $package;
    
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
            'package' => null,
            'middleware' => null,
            'request' => null,
            'exception' => null,
            'response' => null,
        ];
        
        $this->package = $params['package'];
        $this->middleware = $params['middleware'];
        $this->request = $params['request'];
        $this->exception = $params['exception'];
        $this->response = $params['response'];
    }

    /**
     * @return PackageInterface
     */
    public function getPackage()
    {
        return $this->package;
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
