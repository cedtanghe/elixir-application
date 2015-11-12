<?php

namespace Elixir\Foundation\Package;

use Elixir\Foundation\ApplicationInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait PackageTrait
{
    /**
     * @var ApplicationInterface
     */
    protected $application;
    
    /**
     * @var string 
     */
    protected $name;

    /**
     * @var string 
     */
    protected $namespace;

    /**
     * @var string 
     */
    protected $path;
    
    /**
     * @var \ReflectionClass 
     */
    protected $rc;

    /**
     * {@inheritdoc}
     */
    public function getName() 
    {
        if (null === $this->name) 
        {
            $this->name = basename($this->getPath());
        }

        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent() 
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace() 
    {
        if (null === $this->namespace)
        {
            if (null === $this->rc)
            {
                $this->rc = new \ReflectionClass($this);
            }
            
            $this->namespace = $this->rc->getNamespaceName();
        }

        return $this->namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        if (null === $this->path)
        {
            if (null === $this->rc)
            {
                $this->rc = new \ReflectionClass($this);
            }
            
            $this->path = pathinfo($this->rc->getFileName(), PATHINFO_DIRNAME);
        }
        
        return $this->path;
    }
    
    /**
     * {@inheritdoc}
     */
    public function register(ApplicationInterface $application)
    {
        $this->application = $application;
    }
}
