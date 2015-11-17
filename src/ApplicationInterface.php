<?php

namespace Elixir\Foundation;

use Elixir\DI\ContainerInterface;
use Elixir\Dispatcher\DispatcherInterface;
use Elixir\Foundation\PackageInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ApplicationInterface extends HTTPKernelInterface, DispatcherInterface
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
     * @param string $className
     * @return string|null
     */
    public function locateClass($className);

    /**
     * @param string $filePath
     * @param boolean $single
     * @return string|array|null
     */
    public function locateFile($filePath, $single = true);
    
    /**
     * @return boolean
     */
    public function isBooted();
    
    /**
     * @return void
     */
    public function boot();
}
