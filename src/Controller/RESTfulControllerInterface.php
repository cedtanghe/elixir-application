<?php

namespace Elixir\Kernel\Controller;

use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface RESTfulControllerInterface
{
    /**
     * @param string                 $method
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function getRestFulMethodName($method, ServerRequestInterface $request);
}
