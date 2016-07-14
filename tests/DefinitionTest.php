<?php

use \suffi\di\Container;
use \suffi\di\Definition;

/** @TODO Fixtures */
class Foo{

    public $foo = '';
    public $bar = '';

}

class Bar{

    protected $foo = '';
    protected $bar = '';

    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }


}

class Thy{

    protected $foo = '';
    protected $bar = '';

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

}

/**
 * Class DefinitionTest
 */
class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testProperties()
    {
        $container = new Container();

        $def = new Definition($container, 'foo', 'Foo');

        $def->property('foo', 'foo')
            ->property('bar', 'bar');

        $foo = $def->make();

        $this->assertInstanceOf('Foo', $foo);

        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'bar');

        $def->property('foo', 'foo1')
            ->property('bar', 'bar1');

        $foo1 = $def->make();

        $this->assertInstanceOf('Foo', $foo1);

        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'bar');

        $this->assertEquals($foo1->foo, 'foo1');
        $this->assertEquals($foo1->bar, 'bar1');
    }

}
