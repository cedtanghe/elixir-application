<?php

namespace Elixir\Kernel\Exception;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ForbiddenException extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = 'Forbidden')
    {
        parent::__construct($message, 403);
    }
}
