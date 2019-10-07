<?php

namespace suffi\di\Tests\Mocks;

class Foo
{
    private $container = null;

    public $foo = '';
    public $bar = '';

    private $pfoo = '';

    public static $s_foo = '';

    public static function SFoo()
    {
        return new Foo();
    }

    public function setFoo()
    {
    }

    /**
     * @return
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}
