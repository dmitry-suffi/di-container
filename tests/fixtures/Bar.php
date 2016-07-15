<?php


class Bar{

    protected $foo = '';
    protected $bar = '';
    protected $thy = '';

    public function __construct($foo, $bar, $thy = 'thy')
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->thy = $thy;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function getThy()
    {
        return $this->thy;
    }

}