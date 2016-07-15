Определение
===========

**Class suffi\di\Definition**

Class-definition. It contains the definition of the class dependencies and the mechanics of creating a new instance.

### Основные методы:

* ___construct(Container $container, string $name, string $className)_ - Constructor. Options - Container name, class name.

* _parameter(string $paramName, $paramValue)_ - Adding a parameter to the constructor (dependency through the constructor). If the constructor requires an object and a string is given, it will be searched (created) object for this line in the container.

* _property(string $paramName, $paramValue)_ - Adding properties (dependence through the property). If the object should be, then it must be specified explicitly.

* _setter(string $paramName, $paramValue)_ - Adding setter (dependence through method). The method name is constructed from the name of the parameter (eg, setFoo () to foo parameter). If the method requires an object and a string is given, it will be searched (created) object for this line in the container.

* _init(string $methodName)_ - Adding initialization method. The method will be called after object creation and installation of all the properties and setters.

* _make()_ - It creates an object and dependency resolution.

### Примечания.

* setters and initialization methods must be public and abstract.

* If the function is of type object, you can pass the key on which the object will be searched in the container. Only dependency through a constructor or setter. It does not work for the properties, as in php can not explicitly specify the type of the property.