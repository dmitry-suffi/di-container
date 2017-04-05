<?php

namespace suffi\di;

/**
 * Class Container
 * @package suffi\di
 */
class Container
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
     * Set alias
     * @param $name
     * @param $alias
     */
    public function setAlias(string $name, string $alias)
    {
        $this->aliases[$name] = $alias;
    }

    /**
     * Get alias
     * @param $name
     * @return bool|mixed
     */
    public function getAlias(string $name)
    {
        return $this->aliases[$name] ?? false;
    }

    /**
     * Has alias
     * @param $name
     * @return bool
     */
    public function hasAlias(string $name)
    {
        return isset($this->aliases[$name]);
    }

    /**
     * Remove alias
     * @param $name
     */
    public function removeAlias(string $name)
    {
        unset($this->aliases[$name]);
    }

    /**
     * Set definition
     * @param string $name
     * @param string $className
     * @return Definition
     */
    public function setDefinition(string $name, string $className): Definition
    {
        if (!isset($this->definitions[$name])) {
            $this->definitions[$name] = new Definition($this, $name, $className);
        }

        return $this->definitions[$name];
    }

    /**
     * Get definition by name
     * @param string $name
     * @return Definition|false
     */
    public function getDefinition(string $name)
    {
        return $this->definitions[$name] ?? ($this->hasAlias($name) ? $this->getDefinition($this->getAlias($name)) : false);
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
        return isset($this->definitions[$name]) || ($this->hasAlias($name) && $this->hasDefinition($this->getAlias($name)));
    }

    /**
     * Set in container
     * @param string $key
     * @param $instance
     * @throws Exception
     */
    public function set(string $key, $instance)
    {
        if (!is_object($instance)) {
            throw new Exception('Value is not object');
        }

        /** is not singleton */
        $this->container[$key] = $instance;
    }

    /**
     * Get by key
     * @param string $key
     * @return object|false
     */
    public function get(string $key)
    {
        if (isset($this->singletones[$key])) {
            return $this->singletones[$key];
        }

        if (isset($this->container[$key])) {
            return $this->container[$key];
        }

        if (isset($this->definitions[$key])) {
            $this->container[$key] = $this->definitions[$key]->make();
            return $this->container[$key];
        }

        if (isset($this->aliases[$key])) {
            return $this->get($this->aliases[$key]);
        }

        return false;
    }

    /**
     * Has instance by key
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return $this->hasSingleton($key) || isset($this->container[$key]) || ($this->hasAlias($key) && $this->has($this->getAlias($key)));
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
     * @throws Exception
     */
    public function setSingleton(string $key, $instance)
    {
        if ($this->hasSingleton($key)) {
            throw new Exception($key . ' is singleton!');
        }

        if (!is_object($instance)) {
            throw new Exception('Value is not object');
        }

        $this->singletones[$key] = $instance;
    }

    /**
     * Get singleton by key
     * @param string $key
     * @return Object|false
     */
    protected function getSingleton(string $key)
    {
        return $this->singletones[$key] ?? false;
    }

    /**
     * Remove singleton by $key
     * @param string $key
     */
    protected function removeSingleton(string $key)
    {
        unset($this->singletones[$key]);
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

}