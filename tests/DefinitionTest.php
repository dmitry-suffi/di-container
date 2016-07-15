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

}
