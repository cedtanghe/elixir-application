<?php

namespace Elixir\Kernel;

use Elixir\Dispatcher\Event;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;
use Elixir\Kernel\Middleware\MiddlewareInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class HTTPKernelEvent extends Event
{
    /**
     * @var string
     */
    const MIDDLEWARE = 'middleware';

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
     *
     * @param array $params
     */
    public function __construct($type, array $params = [])
    {
        parent::__construct($type);

        $params += [
            'middleware' => null,
            'request' => null,
            'exception' => null,
            'response' => null,
        ];

        $this->middleware = $params['middleware'];
        $this->request = $params['request'];
        $this->exception = $params['exception'];
        $this->response = $params['response'];
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
