<?php

class FooBar implements Barable{

    public $foo = '';

    public function __construct(Barable $foo)
    {
        $this->foo = $foo;
    }


}