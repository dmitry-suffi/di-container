Builder
=======

**Class suffi\di\Builder\Builder**

Абстрактный класс для создания контейнера по конфигурации. Есть реализация `Class suffi\di\Builder\ArrayBuilder`, создающая контейнер по кинфигу в виде массива.

### Основные методы:

* _build():Container_- Создание контейнера.

* _merge(Container $container)_ - Добавление в уже существующий контейнер

### Пример
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