Контейнер внедрения зависимостей
================================

Контейнер внедрения зависимостей — это объект для создания экземпляра классов и зависимых от него объектов.

Данный контейнер поддерживает три типа зависимостей:

* Внедрение зависимости через конструктор;
* Внедрение зависимости через свойство;
* Внедрение зависимости через сеттер.

### Пример использования:

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

Аналогично можно было бы написать:

```php
use suffi\di\Container;

$foo = new Foo();
$bar = new Bar();
$thy = new Thy();

$common = new Common($foo);
$common->bar = $bar;
$common->setThy($thy);

```

Добавление в контейнер объекта:
```php

$container->set('foo', $foo);

```

Добавление в контейнер объекта-синглтона. Объект по данному ключу нельзя будет перезаписать.
```php

$container->setSingleton('foo', $foo);

```

Добавление определения для создания объекта. Если объекта с ключом в контейнере нет, тоон будет создан с помощью заданного определения. Подробнее об [определении](docs/ru/definition.md)
```php

$container->setDefinition('foo', $foo);

```

Получение значения:
```php

$container->get('foo');

```

Подробнее о [контейнере](docs/ru/container.md)

### Также

[Трейт](docs/ru/containerTrait.md) для подключения контейнера в класс