<?php

namespace Elixir\Foundation\Controller;

use Elixir\Foundation\Exception\ErrorException;
use Elixir\Foundation\Exception\ForbiddenException;
use Elixir\Foundation\Exception\NotFoundException;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait ControllerTrait
{
    /**
     * @param string $message
     * @throws NotFoundException
     */
    protected function throwNotFound($message = 'Not Found')
    {
        throw new NotFoundException($message);
    }
    
    /**
     * @param string $message
     * @throws ForbiddenException
     */
    protected function throwForbidden($message = 'Forbidden')
    {
        throw new ForbiddenException($message);
    }
    
    /**
     * @param string $message
     * @throws ErrorException
     */
    protected function throwError($message = 'Internal Server Error')
    {
        throw new ErrorException($message);
    }
}
