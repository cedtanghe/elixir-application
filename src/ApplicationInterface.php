<?php

namespace Elixir\Kernel;

use Elixir\DI\ContainerInterface;
use Elixir\Kernel\Module\ModuleInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ApplicationInterface extends HTTPKernelInterface, LocatorInterface
{
    /**
     * @return ContainerInterface
     */
    public function getContainer();

    /**
     * @param ModuleInterface $module
     */
    public function register(ModuleInterface $module);

    /**
     * @param string $name
     *
     * @return ModuleInterface|null
     */
    public function getModule($name);

    /**
     * @return array
     */
    public function getModules();

    /**
     * @param string $name
     * @param bool   $root
     *
     * @return array|null
     */
    public function getHierarchy($name, $root = false);

    /**
     * @return bool
     */
    public function isBooted();

    public function boot();
}
