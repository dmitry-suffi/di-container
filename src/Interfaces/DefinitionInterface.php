<?php

namespace suffi\di\Interfaces;

use suffi\di\ContainerException;

interface DefinitionInterface
{

    /**
     * @return ContainerInterface
     */
    public function getContainer();

    /**
     * Add dependence through the constructor
     * @param string $paramName
     * @param $paramValue
     * @return $this
     */
    public function parameter(string $paramName, $paramValue);

    /**
     * Add dependence through the property
     * @param string $paramName
     * @param $paramValue
     * @return $this
     */
    public function property(string $paramName, $paramValue);

    /**
     * Add dependence through setter
     * @param string $paramName
     * @param $paramValue
     *
     *      $paramName - name Property.
     * <pre>
     *      Example:
     *          Property: $foo - setter: setFoo()
     *
     *          Property: $_foo - setter: setFoo()
     *
     *          Property: $foo-bar - setter: setFooBar()
     *
     *          Property: $foo_bar - setter: setFooBar()
     * </pre>
     * @return $this
     */
    public function setter(string $paramName, $paramValue);

    /**
     * Add initialization method. Method is called after the object and setting properties
     * @param string $methodName
     * @return $this
     */
    public function init(string $methodName);

    /**
     * Set factory method
     * @param string $factory
     * @return $this
     */
    public function factory(string $factory);
}
