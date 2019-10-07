<?php

namespace suffi\di\Tests;

use suffi\di\Container;
use suffi\di\Definition;
use suffi\di\ExtendedContainer;

class ExtendedContainerTest extends \PHPUnit\Framework\TestCase
{
    public function testExtended()
    {
        $container = new Container();
        $foo = new Mocks\Foo();
        $foo->foo = 'foo';
        $bar = new Mocks\Bar('foo', 'bar');
        $thy = new Mocks\Thy();
        $thy->setFoo('foo');
        $thy->setBar('bar');
        $container->set('foo', $foo);
        $container->setSingleton('bar', $bar);
        $container->set('thy', $thy);
        $container->addDefinition('common', 'Common')
            ->parameter('foo', 'foo')
            ->property('bar', $bar)
            ->setter('thy', 'thy');
        $this->assertFalse($container->has('common'));

        $extContainer = new ExtendedContainer();
        $this->assertFalse($extContainer->has('foo'));
        $this->assertFalse($extContainer->has('bar'));
        $this->assertFalse($extContainer->hasSingleton('bar'));
        $this->assertFalse($extContainer->has('thy'));
        $this->assertFalse($extContainer->has('common'));
        $this->assertFalse($extContainer->hasDefinition('common'));

        $extContainer->setParentsContainer($container);
        $this->assertTrue($extContainer->has('foo'));
        $this->assertTrue($extContainer->has('bar'));
        $this->assertTrue($extContainer->hasSingleton('bar'));
        $this->assertTrue($extContainer->has('thy'));
        $this->assertFalse($extContainer->has('common'));
        $this->assertTrue($extContainer->hasDefinition('common'));
        $this->assertEquals($extContainer->getParentsContainer(), $container);
        $newFoo = $extContainer->get('foo');
        $this->assertInstanceOf(Mocks\Foo::class, $newFoo);
        $this->assertEquals($foo, $newFoo);
        $def = $extContainer->getDefinition('common');
        $this->assertInstanceOf(Definition::class, $def);
        $this->assertEquals($container->getDefinition('common'), $def);
        $this->assertEquals($extContainer->get('bar'), $bar);

        $extContainer->set('foobar', $foo);
        $newFoo = $extContainer->get('foobar');
        $this->assertInstanceOf(Mocks\Foo::class, $newFoo);
    }

    public function testNotFoundParent()
    {
        $container = new Container();
        $extContainer = new ExtendedContainer();
        $extContainer->setParentsContainer($container);

        $this->expectException(\suffi\di\NotFoundException::class);
        $extContainer->get('foo');
    }

    public function testNotFound()
    {
        $extContainer = new ExtendedContainer();

        $this->expectException(\suffi\di\NotFoundException::class);
        $extContainer->get('foo');
    }

    public function testExtendedParameters()
    {
        $container = new Container();
        $extContainer = new ExtendedContainer();

        $container->setParameter('foo', 'foo');
        $container->setParameter('bar', 'bar');

        $this->assertFalse($extContainer->hasParameter('foo'));
        $this->assertFalse($extContainer->hasParameter('bar'));

        $this->assertFalse($extContainer->getParameter('foo'));
        $this->assertFalse($extContainer->getParameter('bar'));

        $extContainer->setParentsContainer($container);

        $this->assertTrue($extContainer->hasParameter('foo'));
        $this->assertTrue($extContainer->hasParameter('bar'));

        $this->assertEquals($extContainer->getParameter('foo'), 'foo');
        $this->assertEquals($extContainer->getParameter('bar'), 'bar');

        $extContainer->setParameter('bar', 'foo');
        $this->assertEquals($extContainer->getParameter('bar'), 'foo');

        $extContainer->removeParameter('bar');
        $this->assertEquals($extContainer->getParameter('bar'), 'bar');

        $extContainer->setParameter('bar', false);
        $this->assertEquals($extContainer->getParameter('bar'), false);
    }
}
