Container
=========

**Class suffi\di\Container**

Class for storing objects and their dependencies.

### Basic methods:

* _set(string $key, $instance)_ - Adding to a container object.

* _setSingleton(string $key, $instance)_ - Adding an object to a container as a singleton. The object can not be deleted or overwritten again.

* _get(string $key)_ - Getting on the key object.

* _make(string $name, array $makeParameters = [])_ - Creating an object by definition, with parameters for the constructor. Does not add the created object to the container.

* _invokeMethod($instance, string $methodName, array $makeParameters = [])_ - Calls a method of an object with substitution of parameters from the container.

* _has(string $key)_ - Indicates whether a container in the finished object with the specified key.

* _hasSingleton(string $key)_ - It indicates whether an object in a container ready for a single specified key.

* _remove(string $key)_ - It removes from the container the object by key. Do not remove the singletons.

* _addDefinition(string $name, string $className): Definition_ - $name specifies the definition for the class $className.

* _setDefinition(string $name, Definition $definition)_ - Set the definition of $definition.

* _getDefinition(string $name)_ - Getting definition by name.

* _removeDefinition(string $name)_ - Removing definition.

* _hasDefinition(string $name)_ - Indicates whether a container in the definition with the specified name.

* _setAlias(string $name, string $alias)_ - Setting alias.

* _getAlias(string $name)_ - Getting alias.

* _hasAlias(string $name)_ - Check for the existence of an alias.

* _removeAlias(string $name)_ - Deleting an alias.

* _setParameter(string $name, string $parameter)_ - Adding a parameter.

* _getParameter(string $name)_ - Getting parameter by name.

* _removeParameter(string $name)_ - Removing parameter.

* _hasParameter(string $name)_ - Checks if the specified parameter exists.