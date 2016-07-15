<?php

namespace suffi\di;

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

    /** @var string initMethod */
    protected $initMethod = '';

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
     *      $paramName - name prorerty.
     *
     *      Example:
     *          Prorerty: $foo - setter: setFoo()
     *
     *          Prorerty: $_foo - setter: setFoo()
     *
     *          Prorerty: $foo-bar - setter: setFooBar()
     *
     *          Prorerty: $foo_bar - setter: setFooBar()
     *
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
                    $paramValue = $this->parameters[$param->getName()];

                    /** If is object type */
                    if (is_string($paramValue) && $param->hasType() && $param->getClass() != null) {
                        $parameters[] = $this->resolve($paramValue);
                    } else {

                        $parameters[] = $paramValue;
                    }

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

                $parameters = $method->getParameters();
                if (!isset($parameters[0])) {
                    throw new Exception(sprintf('Method %s has no input parameters', $settersName));
                }

                $param = $parameters[0];
                if (is_string($value) && $param->hasType() && $param->getClass() != null) {
                    $value = $this->resolve($value);
                }

                if ($method->isStatic()) {
                    $method->invokeArgs(null, [$value]);
                } else {
                    $method->invokeArgs($instance, [$value]);
                }
            }

        }

        /** Init */
        if ($this->initMethod) {
            if (!method_exists($instance, $this->initMethod)) {
                throw new Exception(sprintf('Method %s is not found in class %s', $this->className, $this->initMethod));
            }

            $method = $reflection->getMethod($this->initMethod);

            if ($method->isAbstract()) {
                throw new Exception(sprintf('%s:%s - abstract class method', $this->className, $this->initMethod));
            }
            if (!$method->isPublic()) {
                throw new Exception(sprintf('%s:%s is not public method', $this->className, $this->initMethod));
            }

            if ($method->isStatic()) {
                $method->invokeArgs(null, []);
            } else {
                $method->invokeArgs($instance, []);
            }
        }

        return $instance;
    }

    /**
     * Add initialization method. Method is called after the object and setting properties
     * @param string $methodName
     */
    public function init(string $methodName)
    {
        $this->initMethod = $methodName;
    }

    /**
     * Create instance for className.
     * @todo пока только для конструктора и сеттеров, возможно сделать для публичных свойств ?
     * @param string $className
     * @return object
     * @throws Exception
     */
    protected function resolve(string $className)
    {
        if ($this->container->has($className)) {
            return $this->container->get($className);
        }

        if ($this->container->hasDefinition($className)) {
            return $this->container->getDefinition($className)->make();
        }

        throw new Exception(sprintf('Definition for %s is not found', $className));
    }

}