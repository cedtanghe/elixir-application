<?php

namespace Elixir\Kernel\Middleware;

use Elixir\DI\ContainerAwareInterface;
use Elixir\DI\ContainerInterface;
use Elixir\HTTP\ResponseInterface;
use Elixir\HTTP\ServerRequestInterface;
use Elixir\Kernel\LocatorAwareInterface;
use Elixir\Kernel\LocatorInterface;
use Elixir\MVC\Exception\NotFoundException;
use Elixir\Routing\Route;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class RouterMiddleware implements MiddlewareInterface, ContainerAwareInterface, TerminableInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundException
     */
    public function __invoke($request, $response, callable $next)
    {
        if ($request->isMainRequest()) {
            $router = $this->container->get('Elixir\Routing\RouterInterface');
            $kernel = $this->container->get('kernel');

            $routeMatch = $router->match(trim($request->getPathInfo(), '/'));

            if (null !== $routeMatch) {
                $request = $request->withAttributes(['route_name' => $routeMatch->getRouteName()] + $routeMatch->all() + $request->getAttributes());

                if ($routeMatch->has(Route::MIDDLEWARES)) {
                    $this->middlewares = $routeMatch->get(Route::MIDDLEWARES);
                    $kernelIsLocator = $kernel instanceof LocatorInterface;

                    foreach ($this->middlewares as $middleware) {
                        if ($middleware instanceof ContainerAwareInterface) {
                            $middleware->setContainer($this->container);
                        }

                        if ($kernelIsLocator && $middleware instanceof LocatorAwareInterface) {
                            $middleware->setLocator($kernel);
                        }
                    }

                    $pipelineMiddleware = new PipelineMiddleware(new Pipeline($middlewares));

                    return $pipelineMiddleware($request, $response, function ($request, $response) use ($next) {
                        return $next($request, $response);
                    });
                }
            } else {
                throw new NotFoundException('No route found.');
            }
        }

        return $next($request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($request->isMainRequest() && count($this->middlewares) > 0) {
            $middlewares = array_reverse($this->middlewares);

            foreach ($middlewares as $middleware) {
                if ($middleware instanceof TerminableInterface) {
                    $middleware->terminate($request, $response);
                }
            }
        }
    }
}
