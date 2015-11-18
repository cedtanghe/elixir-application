<?php

namespace Elixir\Foundation;

use Elixir\DI\ContainerInterface;
use Elixir\Dispatcher\DispatcherInterface;
use Elixir\Foundation\LocatorInterface;
use Elixir\Foundation\Package\PackageInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ApplicationInterface extends HTTPKernelInterface, LocatorInterface, DispatcherInterface
{
    /**
     * @return ContainerInterface
     */
    public function getContainer();

    /**
     * @param PackageInterface $package
     */
    public function register(PackageInterface $package);
    
    /**
     * @param string $name
     * @return PackageInterface|null
     */
    public function getPackage($name);

    /**
     * @return array
     */
    public function getPackages();
    
    /**
     * @param string $name
     * @param boolean $root
     * @return array|null
     */
    public function getHierarchy($name, $root = false);
    
    /**
     * @return boolean
     */
    public function isBooted();
    
    /**
     * @return void
     */
    public function boot();
}
