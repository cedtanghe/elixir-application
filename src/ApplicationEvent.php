<?php

namespace Elixir\Foundation;

use Elixir\Dispatcher\Event;
use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\Foundation\Package\PackageInterface;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

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
    const HANDLE = 'handle';
    
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
            'response' => null,
        ];
        
        $this->package = $params['package'];
        $this->middleware = $params['middleware'];
        $this->request = $params['request'];
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
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
