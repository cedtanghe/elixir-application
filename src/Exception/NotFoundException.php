<?php

namespace Elixir\Kernel\Exception;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class NotFoundException extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = 'Not Found')
    {
        parent::__construct($message, 404);
    }
}
