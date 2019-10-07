<?php

namespace suffi\di\Tests\Mocks;

class Common
{

    protected $foo = '';
    public $bar = '';
    protected $thy = '';

    public function __construct(Foo $foo = null)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getThy()
    {
        return $this->thy;
    }

    public function setThy(Thy $thy)
    {
        $this->thy = $thy;
    }

    public function initAll()
    {
        $this->foo = 'foo init';
        $this->bar = 'bar init';
        $this->thy = 'thy init';
    }

    public static function SCommon(Foo $foo = null):Common
    {
        return new Common($foo);
    }

    public static function SBCommon(Foo $foo = null):Common
    {
        $common = new Common($foo);
        $common->bar = 'factory bar';
        return $common;
    }
}
