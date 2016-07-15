Container
=========

**Class suffi\di\Container**

Class for storing objects and their dependencies.

### Basic methods:

* _set(string $key, $instance)_ - Adding to a container object.

* _setSingleton(string $key, $instance)_ - Adding an object to a container as a singleton. The object can not be deleted or overwritten again.

* _get(string $key)_ - Getting on the key object.

* _has(string $key)_ - Indicates whether a container in the finished object with the specified key.

* _hasSingleton(string $key)_ - It indicates whether an object in a container ready for a single specified key.

* _remove(string $key)_ - It removes from the container the object by key. Do not remove the singletons.

* _setDefinition(string $name, string $className): Definition_ - $name specifies the rules for the class $className.

* _getDefinition(string $name)_ - We get the right name.

* _removeDefinition(string $name)_ - Removing rules.

* _hasDefinition(string $name)_ - Indicates whether a container in the rule with the specified name.