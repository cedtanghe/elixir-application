<?php

namespace Elixir\Kernel;

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
