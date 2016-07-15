<?php

use suffi\di\Container;
use suffi\di\Definition;

class ContainerTest extends \PHPUnit_Framework_TestCase
{

    protected function initException()
    {
        $this->expectException(\suffi\di\Exception::class);
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

    public function testSingleton()
    {
        $container = new Container();

        $foo = new Foo();
        $foo->foo = 'foo';

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->hasSingleton('foo'));

        $container->setSingleton('foo', $foo);

        $this->assertTrue($container->has('foo'));
        $this->assertTrue($container->hasSingleton('foo'));

        $newFoo = $container->get('foo');
        $this->assertInstanceOf('Foo', $newFoo);
        $this->assertEquals($foo, $newFoo);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testErrorSingleton()
    {
        $container = new Container();

        $foo = new Foo();
        $foo->foo = 'foo';

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->hasSingleton('foo'));

        $container->setSingleton('foo', $foo);

        $this->assertTrue($container->has('foo'));
        $this->assertTrue($container->hasSingleton('foo'));

        $newFoo = $container->get('foo');
        $this->assertInstanceOf('Foo', $newFoo);
        $this->assertEquals($foo, $newFoo);

        $this->initException();
        $container->setSingleton('foo', $foo);
    }
    
    public function testGet()
    {
        $container = new Container();

        $foo = new Foo();
        $foo->foo = 'foo';

        $bar = new Bar('foo', 'bar');

        $thy = new Thy();

        $thy->setFoo('foo');
        $thy->setBar('bar');

        $container->set('foo', $foo);
        $container->set('bar', $bar);
        $container->set('thy', $thy);

        $container->setDefinition('common', 'Common')
            ->parameter('foo', 'foo')
            ->property('bar', $bar)
            ->setter('thy', 'thy');

        $this->assertFalse($container->has('common'));

        /** @var Common $common */
        $common = $container->get('common');

        $this->assertInstanceOf('Common', $common);

        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, $bar);
        $this->assertEquals($common->getThy(), $thy);
    }

}
