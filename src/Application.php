<?php

namespace Elixir\Foundation;

use Elixir\DI\ContainerAwareInterface;
use Elixir\DI\ContainerInterface;
use Elixir\Dispatcher\DispatcherTrait;
use Elixir\Foundation\ApplicationEvent;
use Elixir\Foundation\ApplicationInterface;
use Elixir\Foundation\CacheableInterface;
use Elixir\Foundation\LocatorInterface;
use Elixir\Foundation\Middleware\MiddlewareInterface;
use Elixir\Foundation\Middleware\Pipeline;
use Elixir\Foundation\Middleware\TerminableInterface;
use Elixir\Foundation\Package\PackageInterface;
use Elixir\HTTP\ResponseFactory;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Application implements ApplicationInterface, CacheableInterface
{
    use DispatcherTrait;
    
    /**
     * @var array
     */
    protected $middlewares = [];
    
    /**
     * @var array
     */
    protected $packages = [];
    
    /**
     * @var ContainerInterface 
     */
    protected $container;
    
    /**
     * @var array 
     */
    protected $hierarchy = [];
    
    /**
     * @var array 
     */
    protected $classesLoaded = [];
    
    /**
     * @var array 
     */
    protected  $filesLoaded = [];

    /**
     * @var array|\ArrayAccess
     */
    protected $cache;
    
    /**
     * @var string|numeric|null
     */
    protected $cacheVersion = null;
    
    /**
     * @var string 
     */
    protected $cacheKey;
    
    /**
     * @var boolean 
     */
    protected $booted = false;
    
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->container->instance('Elixir\Foundation\ApplicationInterface', $this, ['aliases' => 'application']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * @return array|\ArrayAccess
     */
    public function getCache()
    {
        return $this->cache;
    }
    
    /**
     * @return string|numeric|null
     */
    public function getCacheVersion()
    {
        return $this->cacheVersion;
    }
    
    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadFromCache($cache, $version = null, $key = self::DEFAULT_CACHE_KEY)
    {
        $this->cache = $cache;
        $this->cacheVersion = $version;
        $this->cacheKey = $key;
        
        $data = isset($this->cache[$this->cacheKey]) ? $this->cache[$this->cacheKey]: [];
        $version = isset($data['version']) ? $data['version'] : null;
        
        if (null === $this->cacheVersion || null === $version || $version === $this->cacheVersion)
        {
            if (null !== $version)
            {
                $this->cacheVersion = $version;
            }
            
            $this->classesLoaded = array_merge(
                isset($data['classes']) ? $data['classes'] : [],
                $this->classesLoaded
            );
            
            $this->filesLoaded = array_merge(
                isset($data['files']) ? $data['files'] : [],
                $this->filesLoaded
            );
            
            return true;
        }
        
        return false;
    }
    
    /**
     * return boolean
     */
    public function hasMiddleware(MiddlewareInterface $middleware)
    {
        return in_array($middleware, $this->middlewares, true);
    }
    
    /**
     * {@inheritdoc}
     */
    public function pipe(MiddlewareInterface $middleware)
    {
        if ($middleware instanceof ContainerAwareInterface)
        {
            $middleware->setContainer($this->container);
        }
        
        if ($middleware instanceof LocatorInterface)
        {
            $middleware->setLocator($this);
        }
        
        $this->middlewares[] = $middleware;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }
    
    /**
     * return boolean
     */
    public function hasPackage($name)
    {
        return isset($this->packages[$name]);
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function register(PackageInterface $package)
    {
        if ($this->booted)
        {
            throw new \LogicException('You can not add more packages after booted the application.');
        }
        
        $package->register($this);
        $this->packages[$package->getName()] = $package;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPackage($name)
    {
        return isset($this->packages[$name]) ? $this->packages[$name] : null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPackages()
    {
        return $this->packages;
    }
    
    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function getHierarchy($name, $root = false)
    {
        if(!$this->booted)
        {
            throw new \LogicException('The application must first be booted.');
        }
        
        $package = $this->getPackage($name);
        
        if (null === $package)
        {
            return null;
        }
        
        if ($root)
        {
            $root = $package;

            while ($parent = $package->getParent())
            {
                $root = $this->getPackage($parent);
            }

            return $this->hierarchy[$root->getName()];
        }
        else
        {
            return $this->hierarchy[$package->getName()];
        }
    }
    
    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function locateClass($className)
    {
        if(!$this->booted)
        {
            throw new \LogicException('The application must first be booted.');
        }
        
        if(isset($this->classesLoaded[$className]))
        {
            return $this->classesLoaded[$className];
        }
        
        $search = [];
        $find = function($data, $str) use ($className)
        {
            $classes = [str_replace($str, $data['package']->getNamespace(), $className)];
            
            foreach ($data['children'] as $d)
            {
                $classes += $find($d, $str);
            }
            
            return $classes;
        };
        
        if(false !== strpos($pClassName, '(@') && preg_match('/^\(@([^\)]+)\)/', $className, $matches))
        {
            $hierarchy = $this->getHierarchy($matches[1], false);
            
            if (null !== $hierarchy)
            {
                $search += $find($hierarchy, $matches[0]);
            }
        }
        else
        {
            $search = [$className];
        }
        
        $search = array_reverse($search);
        
        foreach($search as $class)
        {
            if(class_exists($class))
            {
                $this->classesLoaded[$className] = $class;
                return $class;
            }
        }
        
        return null;
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function locateFile($filePath, $single = true)
    {
        if(!$this->booted)
        {
            throw new \LogicException('The application must first be booted.');
        }
        
        if(isset($this->filesLoaded[$filePath]))
        {
            $files = $this->filesLoaded[$filePath];
            return $single ? $files[0] : $files;
        }
        
        $search = [];
        $find = function($data, $str, $path) use ($filePath)
        {
            $files = [str_replace($str, $path ? $data['package']->getPath() : $data['package']->getName(), $filePath)];
            
            foreach ($data['children'] as $d)
            {
                $files += $find($d, $str, $path);
            }
            
            return $files;
        };

        if(false !== strpos($filePath, '(@') && preg_match('/\(@([^\)]+)\)/', $filePath, $matches))
        {
            $hierarchy = $this->getHierarchy($matches[1], false);
            
            if (null !== $hierarchy)
            {
                $search += $find($hierarchy, $matches[0], strpos($filePath, $matches[0]) === 0);
            }
        }
        else
        {
            $search = [$filePath];
        }

        $search = array_reverse($search);
        $files = [];

        foreach($search as $file)
        {
            if(file_exists($file))
            {
                $files[] = $file;
            }
        }
        
        if(count($files) > 0)
        {
            $this->filesLoaded[$filePath] = $files;
            return $single ? $files[0] : $files;
        }
        
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isBooted()
    {
        return $this->booted;
    }
    
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->booted)
        {
            return;
        }
        
        $map = function(PackageInterface $package)
        {
            $packages = [
                'package' => $package,
                'children' => []
            ];
            
            foreach ($this->packages as $n => $p)
            {
                if ($p->getParent() === $package->getName())
                {
                    $packages['children'][$p->getName()] = $map($p);
                }
            }
            
            return $packages;
        };
        
        // Check required and parent packages and create hierarchy
        foreach ($this->packages as $name => $package)
        {
            $required = $package->getRequired();
            
            if (null !== $required)
            {
                foreach ((array)$required as $r)
                {
                    if (!$this->hasPackage($r))
                    {
                        throw new \LogicException(sprintf('The "%s" package requires the use of the "%s" package.', $name, $r));
                    }
                }
            }
            
            $parent = $package->getParent();
            
            if (null !== $parent)
            {
                if (!$this->hasPackage($parent))
                {
                    throw new \LogicException(sprintf('The "%s" package extends the unregistered package "%s".', $name, $parent));
                }
            }
            
            $this->hierarchy[$name] = $map($package);
        }
    }
    
    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function handle(ServerRequestInterface $request)
    {
        if ($request->isMainRequest())
        {
            $this->container->instance('Elixir\HTTP\ServerRequestInterface', $request, ['aliases' => 'request']);
        }
        
        $event = new ApplicationEvent(ApplicationEvent::REQUEST, ['request' => $request]);
        $this->dispatch($event);
        
        $request = $event->getRequest();
        $response = $event->getResponse();
        
        $pipeline = new Pipeline($this->middlewares);
        $response = $pipeline->process($request, $response);
        
        if (is_string($response))
        {
            $response = ResponseFactory::createHTML($response, 200);
        }
        
        $event = new ApplicationEvent(ApplicationEvent::RESPONSE, ['response' => $response]);
        $this->dispatch($event);
        
        $response = $event->getResponse();
        
        if (null === $response)
        {
            throw new \LogicException('No response found.');
        }
        
        return $response;
    }
    
    /**
     * {@inheritdoc}
     */
    public function exportToCache()
    {
        if (null !== $this->cache)
        {
            $this->cache[$this->cacheKey] = [
                'classes' => $this->classesLoaded,
                'files' => $this->filesLoaded,
                'version' => $this->cacheVersion
            ];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function invalidateCache()
    {
        if (null !== $this->cache)
        {
            unset($this->cache[$this->cacheKey]);
            return true;
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function terminate(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->dispatch(new ApplicationEvent(ApplicationEvent::TERMINATE, ['request' => $request, 'response' => $response]));
        $middlewares = array_reverse($this->middlewares);
        
        foreach ($middlewares as $middleware)
        {
            if ($middleware instanceof TerminableInterface)
            {
                $middleware->terminate($request, $response);
            }
        }
    }
}
