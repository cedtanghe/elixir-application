<?php

namespace Elixir\Kernel\Middleware;

use Elixir\DI\ContainerAwareInterface;
use Elixir\DI\ContainerInterface;
use Elixir\HTTP\ResponseInterface;
use Elixir\Security\Firewall\Utils;
use Elixir\STDLib\Facade\I18N;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class FirewallMiddleware implements MiddlewareInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

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
     * @throws \Exception
     */
    public function __invoke($request, $response, callable $next)
    {
        $resource = Utils::createResource($request);
        $request = $request->withAttribute('CURRENT_PAGE', $resource);

        $firewall = $this->container->get('Elixir\Security\Firewall\FirewallInterface');
        $authorize = $firewall->analyze($resource);

        $r = $firewall->applyBehavior($authorize);

        if ($r instanceof ResponseInterface) {
            return $r;
        }

        if (!$r && !$authorize) {
            $message = I18N::__('You do not have permission to access this resource.', ['context' => 'elixir']);
            throw new \Exception($message, 403);
        }

        return $next($request, $response);
    }
}
