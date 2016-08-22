<?php

namespace Elixir\Kernel;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface LocatorInterface
{
    /**
     * @param string $className
     *
     * @return string|null
     */
    public function locateClass($className);

    /**
     * @param string $filePath
     * @param bool   $single
     *
     * @return string|array|null
     */
    public function locateFile($filePath, $single = true);
}
