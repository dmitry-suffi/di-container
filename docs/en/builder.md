Builder
=======

**Class suffi\di\Builder\Builder**

An abstract class for creating a container by configuration. There is an implementation of `Class suffi\di\Builder\ArrayBuilder`, which creates a container for kinfig in the form of an array.

### Основные методы:

* _build():Container_- Create a container.

* _merge(Container $container)_ - Adding to an existing container

### Example
```php

$containerBuilder = new ArrayBuilder([
    'services' => [
        'foo' => [
            'class' => 'vendor/foo/Foo',
            'properties' => [
                '$foo' => '%foo%'
            ],
        ],
    ],
    'aliases' => [
        'foo' => 'vendor/foo/Foo'
    ],
    'parameters' => [
        'foo' => 'bar'
    ]
]);

$container = $containerBuilder->build();
```

Builder
=======

** Class suffi \ di \ Builder \ Builder **

An abstract class for creating a container by configuration. There is an implementation of `Class suffi \ di \ Builder \ ArrayBuilder`, which creates a container for kinfig in the form of an array.

### Basic methods:

* _build (): Container_- Create a container.

* _merge (Container $ container) _ - Adding to an existing container
