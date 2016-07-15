<?php


class Common{

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

}