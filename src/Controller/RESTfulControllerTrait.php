<?php

namespace Elixir\Kernel\Controller;

use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait RESTfulControllerTrait
{
    /**
     * @var bool
     */
    protected $strictMethod = false;

    /**
     * {@inheritdoc}
     */
    public function getRestFulMethodName($method, ServerRequestInterface $request)
    {
        $requestMethod = $request->getMethod();
        $prefixs = [$requestMethod];

        if (!$this->strictMethod) {
            switch ($requestMethod) {
                case 'HEAD':
                    $prefixs[] = 'GET';
                    break;
                case 'PUT':
                case 'PATCH':
                case 'DELETE':
                case 'TRACE':
                case 'CONNECT':
                case 'OPTIONS':
                    $prefixs[] = 'POST';
                    break;
            }
        }

        foreach ($prefixs as $prefix) {
            $m = strtolower($prefix).ucfirst($method);

            if (method_exists($this, $m)) {
                return $m;
            }
        }

        if (method_exists($this, $method)) {
            return $method;
        }

        return $method;
    }
}
