<?php

namespace suffi\di\Tests\Mocks;

class Thy
{

    protected $foo = '';
    protected $bar = '';

    protected static $s_foo = '';

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function setBar($bar)
    {
        $this->bar = $bar;
    }

    public static function getSFoo()
    {
        return self::$s_foo;
    }

    public static function setSFoo($s_foo)
    {
        self::$s_foo = $s_foo;
    }

    private function setFooBar($bar)
    {
        $this->bar = $bar;
    }
}
