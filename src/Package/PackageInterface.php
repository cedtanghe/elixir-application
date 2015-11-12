<?php

namespace Elixir\Foundation\Package;

use Elixir\Foundation\ApplicationInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface PackageInterface 
{
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @return string|null
     */
    public function getParent();
    
    /**
     * @return string
     */
    public function getNamespace();
    
    /**
     * @return string
     */
    public function getPath();
    
    /**
     * @return string|array|null
     */
    public function getRequiredPackages();
    
    /**
     * @param ApplicationInterface $application
     */
    public function register(ApplicationInterface $application);
    
    /**
     * @return void
     */
    public function boot();
}
