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
     * @var
     */
    private $definitions = [];

    private $container = [];

    /**
     * @param string $key
     * @param $name
     */
    public function set(string $key, $name)
    {

    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        
        if (isset($this->singletones[$key])) {
            return $this->make($this->singletones[$key]);
        }
    }

    public function has($key)
    {

    }

    public function find($key)
    {

    }

    public function remove($key)
    {

    }

    public function setDefinition($key, $name)
    {

    }

    public function getDefinition($key)
    {

    }

    public function removeDefinition($key)
    {

    }

    public function hasDefinition($key)
    {

    }

    public function findDefinition($key)
    {

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