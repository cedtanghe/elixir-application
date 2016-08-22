<?php

namespace Elixir\Kernel\Controller;

use Elixir\DI\ContainerInterface;
use Elixir\Filter\FilterInterface;
use Elixir\Helper\HelperInterface;
use Elixir\Kernel\Exception\ErrorException;
use Elixir\Kernel\Exception\ForbiddenException;
use Elixir\Kernel\Exception\NotFoundException;
use Elixir\Validator\ValidatorInterface;
use Elixir\View\Storage\StorageInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait ControllerTrait
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param string $name
     * @param array  $options
     *
     * @return HelperInterface
     *
     * @throws \RuntimeException
     */
    protected function helper($name, array $options = [])
    {
        $helperManager = $this->get('Elixir\Helper\HelperManager');

        if ($helperManager) {
            return $helperManager->get($name, $options);
        }

        throw new \RuntimeException(sprintf('Helper Manager is not defined.', $name));
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return FilterInterface
     *
     * @throws \RuntimeException
     */
    protected function filter($name, array $options = [])
    {
        $filterManager = $this->get('Elixir\Filter\FilterManager');

        if ($filterManager) {
            return $filterManager->get($name, $options);
        }

        throw new \RuntimeException(sprintf('Filter Manager is not defined.', $name));
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return ValidatorInterface
     *
     * @throws \RuntimeException
     */
    protected function validator($name, array $options = [])
    {
        $validatorManager = $this->get('Elixir\Validator\ValidatorManager');

        if ($validatorManager) {
            return $validatorManager->get($name, $options);
        }

        throw new \RuntimeException(sprintf('Validator Manager is not defined.', $name));
    }

    /**
     * @see ContainerInterface::get()
     *
     * @throws \RuntimeException
     */
    protected function get($key, array $options = [], $default = null)
    {
        if (!$this->container) {
            throw new \RuntimeException('Service container is not defined.');
        }

        return $this->container->get($key, $options, $default);
    }

    /**
     * @param string $message
     *
     * @throws NotFoundException
     */
    protected function throwNotFound($message = 'Not Found')
    {
        throw new NotFoundException($message);
    }

    /**
     * @param string $message
     *
     * @throws ForbiddenException
     */
    protected function throwForbidden($message = 'Forbidden')
    {
        throw new ForbiddenException($message);
    }

    /**
     * @param string $message
     *
     * @throws ErrorException
     */
    protected function throwError($message = 'Internal Server Error')
    {
        throw new ErrorException($message);
    }

    /**
     * @param string|StorageInterface $template
     * @param array                   $parameters
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function render($template, array $parameters = [])
    {
        $view = $this->get('Elixir\View\ViewInterface');

        if ($view) {
            return $view->render($template, $parameters);
        }

        throw new \RuntimeException(sprintf('View component is not defined.', $name));
    }
}
