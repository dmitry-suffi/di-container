Трейт контейнера
================

**Class suffi\di\containerTrait**

Трейт, добавляющий в класс контейнер и метод конфигурации.


```php

use \suffi\di\containerTrait;

$class->configure(
    [
        'module' => [
            'class' => 'Module',
            'parameters' => [
                'name1' => 'value'
            ],
            'properties' => [
                'name2' => 'value'
            ],
            'setters' => [
                'name3' => 'value'
            ],
            'init' => 'init'
        ]
    ]
);


```