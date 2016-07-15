<?php

use suffi\di\Container;
use suffi\di\Definition;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testDependensy()
    {
        $container = new Container();

        $thy = new Thy();
        $thy->setFoo('foo');

        $container->set('thy', $thy);
        
        //$this->assertInstanceOf('Thy', $container->get('thy'));
    }

    public function testSingletone()
    {
        $container = new Container();

        $name1 = 'Foo';
        $name2 = 'Bar';

//        $this->assertNull($container->getSingleton($name1));
//        $this->assertNull($container->getSingleton($name2));
    }


    public function testDefinition()
    {
        $container = new Container();

        $container->setDefinition('common', 'Common')
            ->parameter('foo', 'foo')
            ->property('bar', 'bar')
            ->setter('thy', 'thy');

        $this->assertTrue($container->hasDefinition('common'));

        $def = $container->getDefinition('common');
        $this->assertInstanceOf('suffi\di\Definition', $def);

        $container->removeDefinition('common');

        $this->assertFalse($container->hasDefinition('common'));
        $this->assertFalse($container->getDefinition('common'));
    }

    public function testContainer()
    {
        $container = new Container();

        $foo = new Foo();
        $foo->foo = 'foo';

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->has('bar'));

        $container->set('foo', $foo);

        $this->assertTrue($container->has('foo'));

        $newFoo = $container->get('foo');

        $this->assertInstanceOf('Foo', $newFoo);
        $this->assertEquals($newFoo->foo, 'foo');
        $this->assertEquals($newFoo, $foo);

        $foo1 = new Foo();
        $foo1->foo = 'bar';

        $container->set('foo', $foo1);

        $newFoo1 = $container->get('foo');

        $this->assertInstanceOf('Foo', $newFoo1);
        $this->assertEquals($newFoo1->foo, 'bar');
        $this->assertEquals($newFoo1, $foo1);
        $this->assertNotEquals($newFoo1, $foo);

        $this->assertFalse($container->get('bar'));

        $this->assertTrue($container->has('foo'));
        $container->remove('foo');

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->has('bar'));

    }

}
