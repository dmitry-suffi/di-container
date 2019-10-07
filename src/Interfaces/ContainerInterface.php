<?php

namespace suffi\di\Interfaces;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{

    /**
     * Add definition
     * @param string $name
     * @param string $className
     * @return DefinitionInterface
     */
    public function addDefinition(string $name, string $className): DefinitionInterface;
    /**
     * Set definition by name
     * @param string $name
     * @param DefinitionInterface $definition
     */
    public function setDefinition(string $name, DefinitionInterface $definition);

    /**
     * Get definition by name
     * @param string $name
     * @return DefinitionInterface|false
     */
    public function getDefinition(string $name);

    /**
     * Remove definition by name
     * @param string $name
     * @return void
     */
    public function removeDefinition(string $name);

    /**
     * Has definition by name
     * @param string $name
     * @return bool
     */
    public function hasDefinition(string $name);

    /**
     * Set in container
     * @param string $key
     * @param $instance
     * @throws ContainerExceptionInterface
     */
    public function set(string $key, $instance);

    /**
     * Remove instance by key
     * @param string $key
     */
    public function remove(string $key);

    /**
     * Set parameter
     * @param string $name
     * @param $parameter
     */
    public function setParameter(string $name, $parameter);
    /**
     * Get parameter
     * @param string $name
     * @return bool|mixed
     */
    public function getParameter(string $name);

    /**
     * Has parameter
     * @param string $name
     * @return bool
     */
    public function hasParameter(string $name);
    /**
     * Remove parameter
     * @param string $name
     */
    public function removeParameter(string $name);

    /**
     * Create for definition name with parameters
     * @param string $name
     * @param array $makeParameters
     * @return null|object
     */
    public function make(string $name, array $makeParameters = []);
}
