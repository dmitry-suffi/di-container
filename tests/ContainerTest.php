<?php

namespace suffi\di\Tests;

use suffi\di\Container;
use suffi\di\Definition;
use suffi\di\ExtendedContainer;
use suffi\di\Tests\Mocks;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return Container
     */
    protected function getContainer():Container
    {
        $container = new Container();
        return $container;
    }

    protected function initException()
    {
        $this->expectException(\suffi\di\ContainerException::class);
    }

    protected function initNotFoundException()
    {
        $this->expectException(\suffi\di\NotFoundException::class);
    }

    public function testDefinition()
    {
        $container = $this->getContainer();

        $container->addDefinition('common', Mocks\Common::class)
            ->parameter('foo', 'foo')
            ->property('bar', 'bar')
            ->setter('thy', 'thy');

        $this->assertTrue($container->hasDefinition('common'));

        $def = $container->getDefinition('common');
        $this->assertInstanceOf(Definition::class, $def);

        $container->removeDefinition('common');

        $this->assertFalse($container->hasDefinition('common'));
        $this->assertFalse($container->getDefinition('common'));

        $container->setDefinition('common', $def);
        $this->assertTrue($container->hasDefinition('common'));
    }

    public function testContainer()
    {
        $container = $this->getContainer();

        $foo = new Mocks\Foo();
        $foo->foo = 'foo';

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->has('bar'));

        $container->set('foo', $foo);

        $this->assertTrue($container->has('foo'));

        $newFoo = $container->get('foo');

        $this->assertInstanceOf(Mocks\Foo::class, $newFoo);
        $this->assertEquals($newFoo->foo, 'foo');
        $this->assertEquals($newFoo, $foo);

        $foo1 = new Mocks\Foo();
        $foo1->foo = 'bar';

        $container->set('foo', $foo1);

        $newFoo1 = $container->get('foo');

        $this->assertInstanceOf(Mocks\Foo::class, $newFoo1);
        $this->assertEquals($newFoo1->foo, 'bar');
        $this->assertEquals($newFoo1, $foo1);
        $this->assertNotEquals($newFoo1, $foo);

        $this->assertTrue($container->has('foo'));
        $container->remove('foo');

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->has('bar'));

        $this->initNotFoundException();
        $this->assertFalse($container->get('bar'));
    }

    public function testSingleton()
    {
        $container = $this->getContainer();

        $foo = new Mocks\Foo();
        $foo->foo = 'foo';

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->hasSingleton('foo'));

        $container->setSingleton('foo', $foo);

        $this->assertTrue($container->has('foo'));
        $this->assertTrue($container->hasSingleton('foo'));

        $newFoo = $container->get('foo');
        $this->assertInstanceOf(Mocks\Foo::class, $newFoo);
        $this->assertEquals($foo, $newFoo);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testErrorSingleton()
    {
        $container = $this->getContainer();

        $foo = new Mocks\Foo();
        $foo->foo = 'foo';

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->hasSingleton('foo'));

        $container->setSingleton('foo', $foo);

        $this->assertTrue($container->has('foo'));
        $this->assertTrue($container->hasSingleton('foo'));

        $newFoo = $container->get('foo');
        $this->assertInstanceOf(Mocks\Foo::class, $newFoo);
        $this->assertEquals($foo, $newFoo);

        $this->initException();
        $container->setSingleton('foo', $foo);
    }

    public function testErrorSingletonWithSet()
    {
        $container = $this->getContainer();

        $foo = new Mocks\Foo();
        $foo->foo = 'foo';

        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->hasSingleton('foo'));

        $container->setSingleton('foo', $foo);

        $this->assertTrue($container->has('foo'));
        $this->assertTrue($container->hasSingleton('foo'));

        $this->initException();
        $container->set('foo', $foo);
    }

    public function testGet()
    {
        $container = $this->getContainer();

        $foo = new Mocks\Foo();
        $foo->foo = 'foo';

        $bar = new Mocks\Bar('foo', 'bar');

        $thy = new Mocks\Thy();

        $thy->setFoo('foo');
        $thy->setBar('bar');

        $container->set('foo', $foo);
        $container->set('bar', $bar);
        $container->set('thy', $thy);

        $container->addDefinition('common', Mocks\Common::class)
            ->parameter('foo', 'foo')
            ->property('bar', $bar)
            ->setter('thy', 'thy');

        $this->assertFalse($container->has('common'));

        /** @var Mocks\Common $common */
        $common = $container->get('common');

        $this->assertInstanceOf(Mocks\Common::class, $common);

        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, $bar);
        $this->assertEquals($common->getThy(), $thy);
    }

    public function testGetIsSingleton()
    {
        $container = $this->getContainer();

        $container->addDefinition('foo', Mocks\Foo::class)
            ->setSingleton(true);

        $container->addDefinition('common', Mocks\Common::class)
            ->parameter('foo', 'foo');

        $this->assertFalse($container->has('common'));
        $this->assertFalse($container->has('foo'));

        /** @var Mocks\Common $common */
        $common = $container->get('common');

        $this->assertTrue($container->has('foo'));

        $foo = $container->get('foo');
        /** @var Mocks\Foo $foo */
        $foo->bar = 'foo';

        $this->assertEquals($common->getFoo()->bar, $foo->bar);
    }

    public function testGetNotSingleton()
    {
        $container = $this->getContainer();

        $container->addDefinition('foo', Mocks\Foo::class)
            ->setSingleton(false);

        $container->addDefinition('common', Mocks\Common::class)
            ->parameter('foo', 'foo');

        $this->assertFalse($container->has('common'));
        $this->assertFalse($container->has('foo'));

        /** @var Mocks\Common $common */
        $common = $container->get('common');

        $this->assertFalse($container->has('foo'));

        $foo = $container->get('foo');

        $this->assertEquals($common->getFoo()->bar, '');
        /** @var Mocks\Foo $foo */
        $foo->bar = 'foo';

        $this->assertEquals($common->getFoo()->bar, '');
    }

    public function testNotObjectSet()
    {
        $container = $this->getContainer();
        $this->initException();
        $container->set('key', 'value');
    }


    public function testNotObjectSetSingletone()
    {
        $container = $this->getContainer();
        $this->initException();
        $container->setSingleton('key', 'value');
    }

    public function testNotFoundContainer()
    {
        $container = new Container();

        $container->remove('foo');

        $this->initNotFoundException();
        $container->get('foo');
    }

    public function testNotFoundExtended()
    {
        $container = new Container();

        $extContainer = new ExtendedContainer();
        $extContainer->setParentsContainer($container);
        $this->initNotFoundException();
        $extContainer->get('bar');
    }

    public function testImplements()
    {
        $c = new class() implements Mocks\Barable {
            public $foo = 'Barable';
        };
        $container = new Container();
        $container->set(Mocks\Barable::class, $c);
        $container->addDefinition('foobar', Mocks\FooBar::class);

        $this->assertTrue($container->has(Mocks\Barable::class));
        $this->assertTrue($container->hasDefinition('foobar'));
        $foobar = $container->get('foobar');

        $this->assertInstanceOf(Mocks\FooBar::class, $foobar);
        $this->assertInstanceOf(Mocks\Barable::class, $foobar->foo);
    }

    public function testAlias()
    {
        $container = new Container();
        $container->addDefinition('foo', Mocks\Foo::class);
        $this->assertFalse($container->hasDefinition('bar'));
        $container->setAlias('bar', 'foo');
        $container->setAlias('thy', 'bar');
        $this->assertEquals($container->getAlias('thy'), 'bar');
        $this->assertTrue($container->hasAlias('thy'));
        $this->assertTrue($container->hasDefinition('foo'));
        $this->assertTrue($container->hasDefinition('bar'));
        $this->assertTrue($container->hasDefinition('thy'));
        $this->assertEquals($container->getDefinition('foo'), $container->getDefinition('bar'));
        $this->assertEquals($container->getDefinition('foo'), $container->getDefinition('thy'));
        $this->assertEquals($container->get('bar'), $container->get('foo'));
        $this->assertEquals($container->get('thy'), $container->get('foo'));
        $container->removeAlias('thy');
        $this->assertFalse($container->getAlias('thy'));
        $this->assertFalse($container->hasAlias('thy'));
        $this->assertFalse($container->hasDefinition('thy'));
    }

    public function testParameters()
    {
        $container = new Container();

        $this->assertEquals($container->getParameter('foo'), false);
        $this->assertEquals($container->getParameter('bar'), false);
        $this->assertFalse($container->hasParameter('foo'));
        $this->assertFalse($container->hasParameter('bar'));

        $container->setParameter('foo', 'foo');

        $this->assertEquals($container->getParameter('foo'), 'foo');
        $this->assertEquals($container->getParameter('bar'), false);
        $this->assertTrue($container->hasParameter('foo'));
        $this->assertFalse($container->hasParameter('bar'));

        $container->setParameter('bar', 'bar');

        $this->assertEquals($container->getParameter('foo'), 'foo');
        $this->assertEquals($container->getParameter('bar'), 'bar');
        $this->assertTrue($container->hasParameter('foo'));
        $this->assertTrue($container->hasParameter('bar'));

        $container->removeParameter('foo');

        $this->assertEquals($container->getParameter('foo'), false);
        $this->assertEquals($container->getParameter('bar'), 'bar');
        $this->assertFalse($container->hasParameter('foo'));
        $this->assertTrue($container->hasParameter('bar'));
    }

    public function testMake()
    {
        $container = new Container();
        $container->addDefinition('bar', Mocks\Bar::class);

        /** @var Mocks\Bar $bar */
        $bar = $container->make('bar', ['foo' => 'foo123', 'bar' => 'bar23']);

        $this->assertInstanceOf(Mocks\Bar::class, $bar);
        $this->assertEquals($bar->getFoo(), 'foo123');
        $this->assertEquals($bar->getBar(), 'bar23');
        $this->assertEquals($bar->getThy(), 'thy'); //default value

        $this->assertNull($container->make('foo', ['foo' => 'foo123', 'bar' => 'bar23']));
    }
}
