Traits container
================

**Class suffi\di\containerTrait**

Traits are added to the class of the container and the configuration method.


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