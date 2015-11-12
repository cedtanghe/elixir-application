<?php

namespace Elixir\Foundation;

use Elixir\DI\ContainerInterface;
use Elixir\Foundation\PackageInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ApplicationInterface extends HTTPKernelInterface
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
     * @param string $packageName
     * @return array
     */
    public function getHierarchy($packageName);
    
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
