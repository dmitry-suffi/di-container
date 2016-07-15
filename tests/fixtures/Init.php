<?php


class Init{

    public $foo = '';
    public $bar = '';
    public $thy = '';

    public function initAll()
    {
        $this->foo = 'foo';
        $this->bar = 'bar';
        $this->thy = 'thy';
    }

    public function initFoo() {
        $this->foo = 'foo';
    }

    public function initBar() {
        $this->bar = 'bar';
    }

    public function initThy() {
        $this->thy = 'thy';
    }

}