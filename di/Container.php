<?php

namespace suffi\di;

/**
 * Class Container
 * @package suffi\di
 * 
 * ```php
 * 
 * $container = new Container();
 * 
 * $container->set($name, $object);
 *
 * $container->get($name);
 *
 * 
 * 
 * ```
 */
class Container
{
    private $singletones = [];

    /**
     * @var Definition[]
     */
    private $definitions = [];

    private $container = [];

    /**
     * Set definition
     * @param string $name
     * @param string $className
     * @return Definition
     */
    public function setDefinition(string $name, string $className)
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
        return $this->definitions[$name] ?? false;
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
        return isset($this->definitions[$name]);
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
//        if (isset($this->singletones[$key])) {
//            return $this->make($this->singletones[$key]);
//        }
        return $this->container[$key] ?? false;
    }

    /**
     * Has instance by key
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->container[$key]);
    }

    /**
     * Remove instance by key
     * @param $key
     */
    public function remove($key)
    {
        unset($this->container[$key]);
    }

    protected function setSingleton($key, $name)
    {

    }

    /**
     * @param $key
     * @return Object|null
     */
    public function getSingleton($key)
    {

    }

    protected function removeSingleton($key)
    {

    }

    public function hasSingleton($key)
    {

    }

    private function make($object)
    {
        if (is_object($object)) {
            return $object;
        } else {
            $newObject = new $object();
        }
    }

    
}