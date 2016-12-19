Dependency injection container
================================

Dependency injection container — it is an object to instantiate the class and its dependent objects.

It supports the following kinds of dependency injection:

* Constructor injection;
* Property injection;
* Setter injection

### Example:

```php
use suffi\di\Container;

$foo = new Foo();
$bar = new Bar();
$thy = new Thy();

$container->set('foo', $foo);
$container->set('bar', $bar);
$container->set('thy', $thy);

$container->setDefinition('common', 'Common')
    ->parameter('foo', 'foo')
    ->property('bar', $bar)
    ->setter('thy', 'thy');

$common = $container->get('common');
```

Similarly, you would write:

```php
use suffi\di\Container;

$foo = new Foo();
$bar = new Bar();
$thy = new Thy();

$common = new Common($foo);
$common->bar = $bar;
$common->setThy($thy);

```

Adding object container:
```php

$container->set('foo', $foo);

```

Adding a container object singleton. The object for the specified key can not be overwritten.
```php

$container->setSingleton('foo', $foo);

```

Adding a definition to create the object. If an object with the key in the container is not, Toon will be created using the specified definition. For more information on [definition](docs/en/definition.md)
```php

$container->setDefinition('foo', $foo);

```

Getting the value:
```php

$container->get('foo');

```

Read more about [container](docs/en/container.md)

###See also:

[Traits](docs/en/containerTrait.md) to connect the container to the classroom