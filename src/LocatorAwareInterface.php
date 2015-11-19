<?php

namespace Elixir\Kernel;

use Elixir\Kernel\LocatorInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface LocatorAwareInterface 
{
    /**
     * @param LocatorInterface $locator
     */
    public function setLocator(LocatorInterface $locator = null);
}
