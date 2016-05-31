<?php

namespace Elixir\Kernel;

use Elixir\Kernel\HTTPKernelEvent;
use Elixir\Kernel\Module\ModuleInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ApplicationEvent extends HTTPKernelEvent 
{
    /**
     * @var string
     */
    const MODULE = 'module';
    
    /**
     * @var ModuleInterface
     */
    protected $module;
    
    /**
     * {@inheritdoc}
     * @param array $params
     */
    public function __construct($type, array $params = [])
    {
        $params += [
            'module' => null
        ];
        
        parent::__construct($type, $params);
        
        $this->module = $params['module'];
    }

    /**
     * @return ModuleInterface
     */
    public function getModule()
    {
        return $this->module;
    }
}
