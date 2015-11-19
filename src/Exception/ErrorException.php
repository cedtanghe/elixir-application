<?php

namespace Elixir\Kernel\Exception;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ErrorException extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = 'Internal Server Error') 
    {
        parent::__construct($message, 500);
    }
}
