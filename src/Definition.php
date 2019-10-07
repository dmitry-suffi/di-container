<?php

namespace suffi\di;

use suffi\di\Interfaces\DefinitionInterface;

/**
 * Class Definition
 * @package suffi\di
 *
 * Example:
 *
 * $container = new Container();
 *
 * $container->setDefinition('name', $object)
 *
 *      ->parameter($paramName, $paramValue) - Add dependence through the constructor
 *      ->property($paramName, $paramValue) - Add dependence through the property
 *      ->setter($paramName, $paramValue) - Add dependence through setter
 *      ->init($methodName) - Add initialization method
 *
 */
final class Definition implements DefinitionInterface
{
    /** @var Container */
    private $container = null;

    /** @var string Name */
    private $name = '';

    /** @var string className */
    private $className = '';

    /** @var bool singleton */
    private $singleton = false;

    /** @var array */
    private $parameters = [];

    /** @var array */
    private $properties = [];

    /** @var array */
    private $setters = [];

    /** @var string initMethod */
    private $initMethod = '';

    /** @var string factory */
    private $factory = '';

    public function __construct(Container $container, string $name, string $className)
    {
        if (!$name) {
            throw new ContainerException('Name is not found!');
        }

        if (!$className) {
            throw new ContainerException('ClassName is not found!');
        }

        $this->container = $container;
        $this->name = $name;
        $this->className = $className;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Add dependence through the constructor
     * @param string $paramName
     * @param $paramValue
     * @return $this
     */
    public function parameter(string $paramName, $paramValue)
    {
        $this->parameters[$paramName] = $paramValue;
        return $this;
    }

    /**
     * Add dependence through the property
     * @param string $paramName
     * @param $paramValue
     * @return $this
     */
    public function property(string $paramName, $paramValue)
    {
        $this->properties[$paramName] = $paramValue;
        return $this;
    }

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
    public function setter(string $paramName, $paramValue)
    {
        $this->setters[$paramName] = $paramValue;
        return $this;
    }

    /**
     * Add initialization method. Method is called after the object and setting properties
     * @param string $methodName
     * @return $this
     */
    public function init(string $methodName)
    {
        $this->initMethod = $methodName;
        return $this;
    }

    /**
     * Set factory method
     * @param string $factory
     * @return $this
     */
    public function factory(string $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get className
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Set className
     * @param string $className
     */
    public function setClassName(string $className)
    {
        $this->className = $className;
    }

    /**
     * Get parameters
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get parameter by name
     * @param string $name
     * @return mixed|null
     */
    public function getParameter(string $name)
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Get properties
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Get property by name
     * @param string $name
     * @return mixed|null
     */
    public function getProperty(string $name)
    {
        return $this->properties[$name] ?? null;
    }

    /**
     * Get setters
     * @return array
     */
    public function getSetters(): array
    {
        return $this->setters;
    }

    /**
     * Get setter by name
     * @param string $name
     * @return mixed|null
     */
    public function getSetter(string $name)
    {
        return $this->setters[$name] ?? null;
    }

    /**
     * Get init method
     * @return string
     */
    public function getInit(): string
    {
        return $this->initMethod;
    }

    /**
     * Get factory method
     * @return string
     */
    public function getFactory(): string
    {
        return $this->factory;
    }

    /**
     * Is singleton
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * Set singleton
     * @param bool $singleton
     * @return $this
     */
    public function setSingleton(bool $singleton)
    {
        $this->singleton = $singleton;
        return $this;
    }
}
