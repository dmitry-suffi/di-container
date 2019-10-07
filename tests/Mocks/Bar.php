<?php

namespace suffi\di\Tests\Mocks;

use suffi\di\Container;

class Bar
{

    /**
     * @var Container
     */
    private $container;

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

    public static function SBar($foo, $bar):Bar
    {
        return new Bar($foo, $bar);
    }

    private static function PBar($foo, $bar):Bar
    {
        return new Bar($foo, $bar);
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
