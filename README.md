Dependency injection container
==============================

[![Build Status](https://api.travis-ci.org/dmitry-suffi/di-container.svg?branch=master)](https://travis-ci.org/dmitry-suffi/di-container)
[![Coveralls](https://coveralls.io/repos/github/dmitry-suffi/di-container/badge.svg?branch=master)](https://coveralls.io/github/dmitry-suffi/di-container?branch=master)

Dependency injection container
[Martin Fowler's article](http://martinfowler.com/articles/injection.html)

Dependency injection container — it is an object to instantiate the class and its dependent objects.

This is in accordance with the recommendations of PSR-11.

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


Installation
------------

```
composer require dmitry-suffi/di
```

Documentation
-------------
the documentation can be found here:
* [English](docs/en)
* [Russian](docs/ru)

Tests
--------
You can also browse the [functional tests](test/)

License
-------

Copyright (c) 2016-2016 Dmitry Suffi.
Released under the [MIT](LICENSE?raw=1) license.