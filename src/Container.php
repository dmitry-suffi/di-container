<?php

namespace suffi\di;

use suffi\di\Interfaces\ContainerInterface;
use suffi\di\Interfaces\DefinitionInterface;

/**
 * Class Container
 * Dependency Container
 * @package suffi\di
 */
class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $singletones = [];

    /**
     * @var Definition[]
     */
    private $definitions = [];

    /**
     * @var array
     */
    private $container = [];

    /**
     * @var array
     */
    private $aliases = [];

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        $this->resolver = new Resolver($this);
    }

    /**
     * @return Resolver
     */
    public function getResolver(): Resolver
    {
        return $this->resolver;
    }

    /**
     * Set alias
     * @param string $name
     * @param $alias
     */
    public function setAlias(string $name, $alias)
    {
        $this->aliases[$name] = $alias;
    }

    /**
     * Get alias
     * @param string $name
     * @return bool|mixed
     */
    public function getAlias(string $name)
    {
        return $this->aliases[$name] ?? false;
    }

    /**
     * Has alias
     * @param string $name
     * @return bool
     */
    public function hasAlias(string $name)
    {
        return isset($this->aliases[$name]);
    }

    /**
     * Remove alias
     * @param string $name
     */
    public function removeAlias(string $name)
    {
        unset($this->aliases[$name]);
    }

    /**
     * Add definition
     * @param string $name
     * @param string $className
     * @return Definition
     */
    public function addDefinition(string $name, string $className): DefinitionInterface
    {
        if (!isset($this->definitions[$name])) {
            $this->definitions[$name] = new Definition($this, $name, $className);
        }

        return $this->definitions[$name];
    }

    /**
     * Set definition by name
     * @param string $name
     * @param Definition $definition
     */
    public function setDefinition(string $name, DefinitionInterface $definition)
    {
        $this->definitions[$name] = $definition;
    }

    /**
     * Get definition by name
     * @param string $name
     * @return Definition|false
     */
    public function getDefinition(string $name)
    {
        return $this->definitions[$name] ??
            ($this->hasAlias($name) ? $this->getDefinition($this->getAlias($name)) : false);
    }

    /**
     * Remove definition by name
     * @param string $name
     * @return void
     */
    public function removeDefinition(string $name)
    {
        unset($this->definitions[$name]);
    }

    /**
     * Has definition by name
     * @param string $name
     * @return bool
     */
    public function hasDefinition(string $name)
    {
        return isset($this->definitions[$name]) ||
            ($this->hasAlias($name) && $this->hasDefinition($this->getAlias($name)));
    }

    /**
     * Set in container
     * @param string $key
     * @param $instance
     * @throws ContainerException
     */
    public function set(string $key, $instance)
    {
        if ($this->hasSingleton($key)) {
            throw new ContainerException($key . ' is singleton!');
        }

        if (!\is_object($instance)) {
            throw new ContainerException('Value is not object');
        }

        /** is not singleton */
        $this->container[$key] = $instance;
    }

    /**
     * Get by key
     * @param string $key
     * @return false|object
     * @throws NotFoundException
     */
    public function get($key)
    {
        $key = (string)$key;

        if (isset($this->singletones[$key])) {
            return $this->singletones[$key];
        }

        if (isset($this->container[$key])) {
            return $this->container[$key];
        }

        if (isset($this->definitions[$key])) {
            $instance = $this->resolver->make($this->definitions[$key]);
            if ($this->definitions[$key]->isSingleton()) {
                $this->singletones[$key] = $instance;
            }
            return $instance;
        }

        if ($this->hasAlias($key)) {
            return $this->get($this->getAlias($key));
        }

        throw new NotFoundException("$key is not found");
    }

    /**
     * Has instance by key
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $key = (string)$key;
        return $this->hasSingleton($key) ||
            isset($this->container[$key]) || ($this->hasAlias($key) && $this->has($this->getAlias($key)));
    }

    /**
     * Remove instance by key
     * @param string $key
     */
    public function remove(string $key)
    {
        unset($this->container[$key]);
    }

    /**
     * Set singleton in container
     * @param string $key
     * @param object $instance
     * @throws ContainerException
     */
    public function setSingleton(string $key, $instance)
    {
        if ($this->hasSingleton($key)) {
            throw new ContainerException($key . ' is singleton!');
        }

        if (!\is_object($instance)) {
            throw new ContainerException('Value is not object');
        }

        $this->singletones[$key] = $instance;
    }

    /**
     * Has singleton by $key
     * @param string $key
     * @return bool
     */
    public function hasSingleton(string $key)
    {
        return isset($this->singletones[$key]);
    }

    /**
     * Set parameter
     * @param string $name
     * @param $parameter
     */
    public function setParameter(string $name, $parameter)
    {
        $this->parameters[$name] = $parameter;
    }

    /**
     * Get parameter
     * @param string $name
     * @return bool|mixed
     */
    public function getParameter(string $name)
    {
        return $this->parameters[$name] ?? false;
    }

    /**
     * Has parameter
     * @param string $name
     * @return bool
     */
    public function hasParameter(string $name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Remove parameter
     * @param string $name
     */
    public function removeParameter(string $name)
    {
        unset($this->parameters[$name]);
    }

    /**
     * Create for definition name with parameters
     * @param string $name
     * @param array $makeParameters
     * @return null|object
     */
    public function make(string $name, array $makeParameters = [])
    {
        if ($this->hasDefinition($name)) {
            return $this->resolver->make($this->getDefinition($name), $makeParameters);
        }
        //@todo create class ???
        return null;
    }

    /**
     * Invokes an object method with parameters in it from the container.
     * @param $instance
     * @param string $methodName
     * @param array $makeParameters
     * @throws ContainerException
     */
    public function invokeMethod($instance, string $methodName, array $makeParameters = [])
    {
        return $this->resolver->invokeMethod($instance, $methodName, $makeParameters);
    }
}
