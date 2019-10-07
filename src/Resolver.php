<?php

namespace suffi\di;

use suffi\di\Interfaces\ContainerInterface;

class Resolver
{

    protected $container;

    /**
     * Resolver constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @todo in container?
     * Get parameter value
     * @param $paramValue
     * @return bool|mixed
     */
    public function getParameterValue($paramValue)
    {
        if (preg_match('/%(.+?)%/', $paramValue)) {
            $parameterName = preg_replace('/%(.+?)%/', '$1', $paramValue);
            $paramValue = $this->container->getParameter($parameterName);
        }
        return $paramValue;
    }

    /**
     * @param $paramValue
     * @return bool
     */
    protected function isCallable($paramValue)
    {
        return !is_string($paramValue) && is_callable($paramValue);
    }

    /**
     * Build parameter
     * @param $paramValue
     * @param \ReflectionParameter $param
     * @return mixed|object
     */
    protected function makeParamValue($paramValue, \ReflectionParameter $param)
    {
        if ($this->isCallable($paramValue)) {
            $paramValue = call_user_func($paramValue);
        }

        /** If is object type */
        if (is_string($paramValue) && $param->hasType() && $param->getClass() != null) {
            return $this->container->get($paramValue);
        } else {
            return $paramValue;
        }
    }

    /**
     * Make instance for definition
     * @param Definition $definition
     * @param array $makeParameters
     * @return mixed|object
     * @throws ContainerException
     */
    public function make(Definition $definition, array $makeParameters = [])
    {
        $className = $definition->getClassName();

        if (!class_exists($className)) {
            throw new ContainerException(sprintf('Class %s not found', $className));
        }

        $reflection = new \ReflectionClass($className);

        /** Constructor */
        $constructor = $this->getConstructor($definition, $reflection);

        /** Create instance */

        if ($constructor !== null) {
            $parameters = array_merge($definition->getParameters(), $makeParameters);
            $constructorParameters = $this->makeMethodParameters($constructor, $parameters);

            $instance = $definition->getFactory() ? $constructor->invokeArgs(null, $constructorParameters) :
                $reflection->newInstanceArgs($constructorParameters);
        } else {
            $instance = new $className;
        }

        /** setContainer */
        $this->setContainer($instance, $reflection);

        /** Properties */
        $this->initProperties($definition, $instance, $reflection);

        /** Setters */
        $this->initSetters($definition, $instance, $reflection);

        /** Init */
        $this->initInitMethod($definition, $instance, $reflection);

        return $instance;
    }

    /**
     * Get constructor method
     * @param \ReflectionClass $reflection
     * @return \ReflectionMethod|null
     * @throws ContainerException
     */
    protected function getConstructor(Definition $definition, \ReflectionClass $reflection)
    {
        $factory = $definition->getFactory();
        if ($factory) {
            if (!$reflection->hasMethod($factory)) {
                throw new ContainerException(sprintf('Method %s not found', $factory));
            }
            $factory = $reflection->getMethod($factory);
            if (!$factory->isStatic()) {
                throw new ContainerException(sprintf('Method %s not static', $factory));
            }
            if (!$factory->isPublic()) {
                throw new ContainerException(sprintf('Method %s not public', $factory));
            }
            $type = $this->getReflectionTypeName($factory->getReturnType());
            if (!in_array($type, [$definition->getClassName(), 'static', 'self'])) {
                throw new ContainerException(
                    sprintf('Method %s not return type of %s', $factory, $definition->getClassName())
                );
            }
            $constructor = $factory;
        } else {
            $constructor = $reflection->getConstructor();
        }
        return $constructor;
    }

    /**
     * @param \ReflectionMethod|null $reflectionMethod
     * @param array $parameters
     * @return array
     * @throws ContainerException
     */
    protected function makeMethodParameters(\ReflectionMethod $reflectionMethod, array $parameters): array
    {
        $methodParameters = [];
        foreach ($reflectionMethod->getParameters() as $parameter) {
            /** The parameter is specified explicitly */
            if (isset($parameters[$parameter->getName()])) {
                $paramValue = $parameters[$parameter->getName()];
                $methodParameters[] = $this->makeParamValue($paramValue, $parameter);
            } elseif (isset($parameters['$' . $parameter->getName()])) {
                $paramValue = $this->getParameterValue($parameters['$' . $parameter->getName()]);
                $methodParameters[] = $this->makeParamValue($paramValue, $parameter);
            } else {
                /** Default value */
                if ($parameter->isDefaultValueAvailable()) {
                    $methodParameters[] = $parameter->getDefaultValue();
                } else {
                    /** If is object type */
                    if ($parameter->hasType() && $parameter->getClass() != null) {
                        $methodParameters[] = $this->container->get($parameter->getClass()->name);
                    } else {
                        /** No is optional */
                        if (!$parameter->isOptional()) {
                            throw new ContainerException(
                                sprintf('Do not set the parameter %s to constructor', $parameter->getName())
                            );
                        }
                    }
                }
            }
        }
        return $methodParameters;
    }

    /**
     * Set container in instance
     * @param $instance
     * @param \ReflectionClass $reflection
     */
    protected function setContainer($instance, \ReflectionClass $reflection)
    {
        if ($reflection->hasMethod('setContainer')) {
            $method = $reflection->getMethod('setContainer');
            if ($method) {
                if ($method->isPublic() && !$method->isStatic() && !$method->isAbstract()) {
                    $parameters = $method->getParameters();
                    if (isset($parameters[0])) {
                        $type = $this->getReflectionTypeName($parameters[0]->getType());
                        if (get_class($this->container) == $type || isset(class_parents($this->container)[$type])) {
                            $instance->setContainer($this->container);
                        }
                    }
                }
            }
        }
    }

    /**
     * Init properties
     * @param Object $instance
     * @param \ReflectionClass $reflection
     * @throws ContainerException
     */
    protected function initProperties(Definition $definition, $instance, \ReflectionClass $reflection)
    {
        foreach ($definition->getProperties() as $name => $value) {
            if (substr($name, 0, 1) === '$') {
                $name = substr($name, 1);
                $value = $this->getParameterValue($value);
            }

            if ($reflection->hasProperty($name)) {
                $property = $reflection->getProperty($name);

                if ($property) {
                    if (!$property->isPublic()) {
                        throw new ContainerException(
                            sprintf('%s Class %s property is not public', $definition->getClassName(), $name)
                        );
                    }

                    if ($this->isCallable($value)) {
                        $value = call_user_func($value);
                    }

                    if ($property->isStatic()) {
                        $property->setValue($value);
                    } else {
                        $property->setValue($instance, $value);
                    }
                }
            }
        }
    }

    /**
     * Init setters
     * @param $instance
     * @param \ReflectionClass $reflection
     * @throws ContainerException
     */
    protected function initSetters(Definition $definition, $instance, \ReflectionClass $reflection)
    {
        foreach ($definition->getSetters() as $name => $value) {
            if (substr($name, 0, 1) === '$') {
                $name = substr($name, 1);
                $value = $this->getParameterValue($value);
            }

            $settersName = 'set' . str_replace(' ', '', ucwords(strtolower(implode(' ', explode(
                '-',
                str_replace('_', '-', $name)
            )))));

            if ($reflection->hasMethod($settersName)) {
                $method = $reflection->getMethod($settersName);
                if ($method) {
                    if (!$method->isPublic()) {
                        throw new ContainerException(
                            sprintf('%s:%s is not public method', $definition->getClassName(), $settersName)
                        );
                    }

                    $parameters = $method->getParameters();
                    if (!isset($parameters[0])) {
                        throw new ContainerException(
                            sprintf('Method %s has no input parameters', $settersName)
                        );
                    }

                    $param = $parameters[0];

                    if ($this->isCallable($value)) {
                        $value = call_user_func($value);
                    }

                    if (is_string($value) && $param->hasType() && $param->getClass() != null) {
                        $value = $this->container->get($value);
                    }

                    if ($method->isStatic()) {
                        $method->invokeArgs(null, [$value]);
                    } else {
                        $method->invokeArgs($instance, [$value]);
                    }
                }
            }
        }
    }


    /**
     * Create and call init method
     * @param Object $instance
     * @param \ReflectionClass $reflection
     * @throws ContainerException
     */
    protected function initInitMethod(Definition $definition, $instance, \ReflectionClass $reflection)
    {
        if ($definition->getInit()) {
            $initMethod = $definition->getInit();
            if (!method_exists($instance, $initMethod)) {
                throw new ContainerException(
                    sprintf('Method %s is not found in class %s', $definition->getClassName(), $initMethod)
                );
            }

            $method = $reflection->getMethod($initMethod);

            if (!$method->isPublic()) {
                throw new ContainerException(
                    sprintf('%s:%s is not public method', $definition->getClassName(), $initMethod)
                );
            }

            if ($method->isStatic()) {
                $method->invokeArgs(null, []);
            } else {
                $method->invokeArgs($instance, []);
            }
        }
    }

    /**
     * Invokes an object method with parameters in it from the container.
     * @param $instance
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     * @throws ContainerException
     */
    public function invokeMethod($instance, string $methodName, array $parameters = [])
    {
        $className = get_class($instance);
        $reflection = new \ReflectionClass($className);

        if ($reflection->hasMethod($methodName)) {
            $method = $reflection->getMethod($methodName);
            if (!$method->isPublic()) {
                throw new ContainerException(
                    sprintf('%s:%s is not public method', $className, $methodName)
                );
            }

            $methodParameters = $this->makeMethodParameters($method, $parameters);

            if ($method->isStatic()) {
                return $method->invokeArgs(null, $methodParameters);
            } else {
                return $method->invokeArgs($instance, $methodParameters);
            }
        } else {
            throw new ContainerException(
                sprintf('%s:%s is not public method', $className, $methodName)
            );
        }
    }

    /**
     * @param $reflectionType
     * @param $parameters
     * @return string
     */
    private function getReflectionTypeName($reflectionType): string
    {
        if (method_exists($reflectionType, 'getName')) {
            $type = $reflectionType->getName();
        } else {
            $type = $reflectionType;
        }
        return $type;
    }
}
