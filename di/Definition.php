<?php

namespace suffi\di;

use phpDocumentor\Reflection\Types\Object_;

/**
 * Class Definition
 * @package suffi\di
 *
 * ```php
 *
 * $container = new Container();
 *
 * $container->setDefinition('name', $object)
 *      ->parameter($paramName, $paramValue) - Зависимость через конструктор
 *      ->property($paramName, $paramValue) - Зависимость через свойство
 *      ->setter($paramName, $paramValue) - Зависимость через сеттер
 *
 *
 * ```
 */
final class Definition
{
    /** @var Container */
    protected $container = null;

    /** @var string Name */
    protected $name = '';

    /** @var string className */
    protected $className = '';

    /** @var array */
    protected $parameters = [];

    /** @var array */
    protected $properties = [];

    /** @var array */
    protected $setters = [];

    public function __construct(Container $container, string $name, string $className)
    {
        if (!$name) {
            throw new \Exception('Name is not found!');
        }

        if (!$className) {
            throw new \Exception('ClassName is not found!');
        }

        $this->container = $container;
        $this->name = $name;
        $this->className = $className;
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
     * @return $this
     */
    public function setter(string $paramName, $paramValue)
    {
        $this->setters[$paramName] = $paramValue;
        return $this;
    }

    /**
     * @return object
     * @throws Exception
     */
    public function make()
    {
        if (!class_exists($this->className)) {
            throw new Exception(sprintf('Class %s not found', $this->className));
        }

        $reflection = new \ReflectionClass($this->className);

        $constructor = $reflection->getConstructor();
        $parameters = [];

        /** Constructor */
        if ($constructor !== null) {
            foreach ($constructor->getParameters() as $param) {
                /** The parameter is specified explicitly */
                if (isset($this->parameters[$param->getName()])) {
                    $parameters[] = $this->parameters[$param->getName()];
                } else {
                    /** Default value */
                    if ($param->isDefaultValueAvailable()) {
                        $parameters[] = $param->getDefaultValue();
                    } else {
                        /** If is object type */
                        if ($param->hasType() && $param->getClass() != null) {
                            $parameters[] = $this->resolve($param->getClass()->name);
                        } else {
                            /** No is optional */
                            if (!$param->isOptional()) {
                                throw new Exception(sprintf('Do not set the parameter %s to constructor', $param->getName()));
                            }
                        }
                    }
                }

            }
        }

        $instance = $reflection->newInstanceArgs($parameters);

        /** Properties */
        foreach ($this->properties as $name => $value) {

            $property = $reflection->getProperty($name);

            if ($property) {
                if (!$property->isPublic()) {
                    throw new Exception(sprintf('%s Class %s property is not public', $this->className, $name));
                }
                if ($property->isStatic()) {
                    $property->setValue($value);
                } else {
                    $property->setValue($instance, $value);
                }
            }
        }

        /** Setters */
        foreach ($this->setters as $name => $value) {
            $settersName = 'set' . str_replace(' ', '', ucwords(strtolower(implode(' ', explode('-', str_replace('_', '-', $name))))));

            $method = $reflection->getMethod($settersName);
            if ($method) {
                if ($method->isAbstract()) {
                    throw new Exception(sprintf('%s:%s - abstract class method', $this->className, $settersName));
                }
                if (!$method->isPublic()) {
                    throw new Exception(sprintf('%s:%s is not public method', $this->className, $settersName));
                }
                if ($method->isStatic()) {
                    $method->invokeArgs(null, [$value]);
                } else {
                    $method->invokeArgs($instance, [$value]);
                }
            }

        }

        return $instance;
    }

    protected function resolve(string $className)
    {
        /** @TODO */
        return new \stdClass();
    }

}