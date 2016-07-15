<?php

use \suffi\di\Container;
use \suffi\di\Definition;

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

        $this->assertInstanceOf('Foo', $foo);
        $this->assertInstanceOf('Foo', $foo1);

        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'bar');

        $this->assertEquals($foo1->foo, 'foo1');
        $this->assertEquals($foo1->bar, 'bar1');

        $def2 = new Definition($container, 'foo', 'Foo');
        $def2->property('foo', 'foo2');

        $foo2 = $def2->make();

        $this->assertInstanceOf('Foo', $foo);
        $this->assertInstanceOf('Foo', $foo1);
        $this->assertInstanceOf('Foo', $foo2);

        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'bar');

        $this->assertEquals($foo1->foo, 'foo1');
        $this->assertEquals($foo1->bar, 'bar1');

        $this->assertEquals($foo2->foo, 'foo2');
        $this->assertEquals($foo2->bar, '');

        /** Static */
        $def->property('s_foo', 'foo');

        $this->assertNotEquals(Foo::$s_foo, 'foo');
        $foo3 = $def->make();

        $this->assertInstanceOf('Foo', $foo3);

        $this->assertEquals(Foo::$s_foo, 'foo');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testPrivateProperty()
    {

        $container = new Container();

        $def = new Definition($container, 'foo', 'Foo');

        $def->property('_foo', 'foo');

        $this->expectException(\suffi\di\Exception::class);
        $def->make();
    }

    public function testConstructor()
    {
        $container = new Container();

        $def = new Definition($container, 'bar', 'Bar');

        $def->parameter('foo', 'foo')
            ->parameter('bar', 'bar');

        /** @var Bar $bar */
        $bar = $def->make();

        $this->assertInstanceOf('Bar', $bar);
        $this->assertEquals($bar->getFoo(), 'foo');
        $this->assertEquals($bar->getBar(), 'bar');
        $this->assertEquals($bar->getThy(), 'thy'); //default value

        $def->parameter('foo', 'foo1')
            ->parameter('bar', 'bar1')
            ->parameter('thy', 'thy1');

        /** @var Bar $bar */
        $bar1 = $def->make();

        $this->assertInstanceOf('Bar', $bar1);
        $this->assertEquals($bar1->getFoo(), 'foo1');
        $this->assertEquals($bar1->getBar(), 'bar1');
        $this->assertEquals($bar1->getThy(), 'thy1');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testNoSetParameters()
    {
        $container = new Container();

        $def = new Definition($container, 'bar', 'Bar');

        $def->parameter('foo', 'foo');

        $this->expectException(\suffi\di\Exception::class);
        $bar = $def->make();
    }

    public function testSetters()
    {
        $container = new Container();

        $def = new Definition($container, 'thy', 'Thy');

        /** @var Thy $thy */
        $thy = $def->make();

        $this->assertInstanceOf('Thy', $thy);
        $this->assertEquals($thy->getFoo(), '');
        $this->assertEquals($thy->getBar(), '');
        $this->assertEquals(Thy::getSFoo(), '');

        $def->setter('foo', 'foo')
            ->setter('bar', 'bar');

        /** @var Thy $thy1 */
        $thy1 = $def->make();

        $this->assertInstanceOf('Thy', $thy1);
        $this->assertEquals($thy1->getFoo(), 'foo');
        $this->assertEquals($thy1->getBar(), 'bar');
        $this->assertEquals(Thy::getSFoo(), '');

        $def->setter('s_foo', 's_foo');

        $def->make();
        $this->assertEquals(Thy::getSFoo(), 's_foo');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testPrivateSetter()
    {

        $container = new Container();

        $def = new Definition($container, 'thy', 'Thy');

        $def->setter('foo-bar', 'foo');

        $this->expectException(\suffi\di\Exception::class);
        $def->make();
    }

}
