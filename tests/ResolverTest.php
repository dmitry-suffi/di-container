<?php

namespace suffi\di\Tests;

use \suffi\di\Container;
use \suffi\di\Definition;
use \suffi\di\Tests\Mocks;

/**
 * Class DefinitionTest
 */
class ResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return Container
     */
    protected function getContainer(): Container
    {
        $container = new Container();
        return $container;
    }

    protected function initException()
    {
        $this->expectException(\suffi\di\ContainerException::class);
    }

    public function testProperties()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'foo', Mocks\Foo::class);

        $def->property('foo', 'foo')
            ->property('bar', 'bar');

        $foo = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Foo::class, $foo);

        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'bar');

        $def->property('foo', 'foo1')
            ->property('bar', 'bar1');

        $foo1 = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Foo::class, $foo);
        $this->assertInstanceOf(Mocks\Foo::class, $foo1);

        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'bar');

        $this->assertEquals($foo1->foo, 'foo1');
        $this->assertEquals($foo1->bar, 'bar1');

        $def2 = new Definition($container, 'foo', Mocks\Foo::class);
        $def2->property('foo', 'foo2');

        $foo2 = $resolver->make($def2);

        $this->assertInstanceOf(Mocks\Foo::class, $foo);
        $this->assertInstanceOf(Mocks\Foo::class, $foo1);
        $this->assertInstanceOf(Mocks\Foo::class, $foo2);

        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'bar');

        $this->assertEquals($foo1->foo, 'foo1');
        $this->assertEquals($foo1->bar, 'bar1');

        $this->assertEquals($foo2->foo, 'foo2');
        $this->assertEquals($foo2->bar, '');

        /** Static */
        $def->property('s_foo', 'foo');

        $this->assertNotEquals(Mocks\Foo::$s_foo, 'foo');
        $foo3 = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Foo::class, $foo3);

        $this->assertEquals(Mocks\Foo::$s_foo, 'foo');

        /** Callable */

        $def = new Definition($container, 'foo', Mocks\Foo::class);

        $def->property('foo', function () {
            return 'foo';
        })
            ->property('bar', function () {
                return 'foo';
            });

        $foo = $resolver->make($def);
        $this->assertEquals($foo->foo, 'foo');
        $this->assertEquals($foo->bar, 'foo');
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testPrivateProperty()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'foo', Mocks\Foo::class);

        $def->property('pfoo', 'foo');

        $this->initException();
        $resolver->make($def);
    }

    public function testConstructor()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'bar', Mocks\Bar::class);

        $def->parameter('foo', 'foo')
            ->parameter('bar', 'bar');

        /** @var Mocks\Bar $bar */
        $bar = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Bar::class, $bar);
        $this->assertEquals($bar->getFoo(), 'foo');
        $this->assertEquals($bar->getBar(), 'bar');
        $this->assertEquals($bar->getThy(), 'thy'); //default value

        $def->parameter('foo', 'foo1')
            ->parameter('bar', 'bar1')
            ->parameter('thy', 'thy1');

        /** @var Mocks\Bar $bar */
        $bar1 = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Bar::class, $bar1);
        $this->assertEquals($bar1->getFoo(), 'foo1');
        $this->assertEquals($bar1->getBar(), 'bar1');
        $this->assertEquals($bar1->getThy(), 'thy1');

        /** Callable */
        $def->parameter('foo', function () {
            return 'foo1bar1';
        })
            ->parameter('bar', 'bar1')
            ->parameter('thy', 'thy1');

        /** @var Mocks\Bar $bar */
        $bar1 = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Bar::class, $bar1);
        $this->assertEquals($bar1->getFoo(), 'foo1bar1');
        $this->assertEquals($bar1->getBar(), 'bar1');
        $this->assertEquals($bar1->getThy(), 'thy1');
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testNoSetParameters()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'bar', Mocks\Bar::class);

        $def->parameter('foo', 'foo');

        $this->initException();
        $resolver->make($def);
    }

    public function testClassNotExist()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'bar', 'NotExistClass');

        $this->initException();
        $resolver->make($def);
    }

    public function testSetters()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'thy', Mocks\Thy::class);

        /** @var Mocks\Thy $thy */
        $thy = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Thy::class, $thy);
        $this->assertEquals($thy->getFoo(), '');
        $this->assertEquals($thy->getBar(), '');
        $this->assertEquals(Mocks\Thy::getSFoo(), '');

        $def->setter('foo', 'foo')
            ->setter('bar', 'bar');

        /** @var Mocks\Thy $thy1 */
        $thy1 = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Thy::class, $thy1);
        $this->assertEquals($thy1->getFoo(), 'foo');
        $this->assertEquals($thy1->getBar(), 'bar');
        $this->assertEquals(Mocks\Thy::getSFoo(), '');

        $def->setter('s_foo', 's_foo');

        $resolver->make($def);
        $this->assertEquals(Mocks\Thy::getSFoo(), 's_foo');

        /** @var Mocks\Thy $thy1 */
        $def->setter('foo', 'foo')
            ->setter('bar', function () {
                return 'foo1bar1';
            });

        $thy2 = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Thy::class, $thy2);
        $this->assertEquals($thy2->getFoo(), 'foo');
        $this->assertEquals($thy2->getBar(), 'foo1bar1');
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testPrivateSetter()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'thy', Mocks\Thy::class);

        $def->setter('foo-bar', 'foo');

        $this->initException();
        $resolver->make($def);
    }

    public function testInit()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'init', Mocks\Init::class);

        /** @var Mocks\Init $init */
        $init = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Init::class, $init);
        $this->assertEquals($init->foo, '');
        $this->assertEquals($init->bar, '');
        $this->assertEquals($init->thy, '');

        $def->init('initFoo');

        /** @var Mocks\Init $init */
        $init = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Init::class, $init);
        $this->assertEquals($init->foo, 'foo');
        $this->assertEquals($init->bar, '');
        $this->assertEquals($init->thy, '');

        $def->init('initBar');

        /** @var Mocks\Init $init */
        $init = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Init::class, $init);
        $this->assertEquals($init->foo, '');
        $this->assertEquals($init->bar, 'bar');
        $this->assertEquals($init->thy, '');

        $def->init('initThy');

        /** @var Mocks\Init $init */
        $init = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Init::class, $init);
        $this->assertEquals($init->foo, '');
        $this->assertEquals($init->bar, '');
        $this->assertEquals($init->thy, 'thy');

        $def->init('initAll');

        /** @var Mocks\Init $init */
        $init = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Init::class, $init);
        $this->assertEquals($init->foo, 'foo');
        $this->assertEquals($init->bar, 'bar');
        $this->assertEquals($init->thy, 'thy');
    }

    public function testSetterException()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'foo', Mocks\Foo::class);
        $def->setter('foo', 123);
        $this->initException();
        $resolver->make($def);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testNoExistInit()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'init', Mocks\Init::class);

        $def->init('initNoExist');

        $this->initException();
        $resolver->make($def);
    }

    public function testPrivateInitException()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'bar', Mocks\Init::class);
        $def->init('initPr');

        $this->initException();
        $resolver->make($def);
    }

    public function testStaticInit()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'bar', Mocks\Init::class);
        $def->init('initSt');

        $this->assertEquals(Mocks\Init::$sFoo, '');
        $bar = $resolver->make($def);
        $this->assertEquals(Mocks\Init::$sFoo, 'foo static');
    }

    public function testCommon()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'common', Mocks\Common::class);

        $foo = new Mocks\Foo();
        $thy = new Mocks\Thy();

        /** @var Mocks\Common $common */
        $common = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Common::class, $common);
        $this->assertEquals($common->getFoo(), '');
        $this->assertEquals($common->bar, '');
        $this->assertEquals($common->getThy(), '');

        $def->parameter('foo', $foo);

        $common = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Common::class, $common);
        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, '');
        $this->assertEquals($common->getThy(), '');

        $def->property('bar', 'bar');

        $common = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Common::class, $common);
        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, 'bar');
        $this->assertEquals($common->getThy(), '');

        $def->setter('thy', $thy);

        $common = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Common::class, $common);
        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, 'bar');
        $this->assertEquals($common->getThy(), $thy);

        $def->init('initAll');

        $common = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Common::class, $common);
        $this->assertEquals($common->getFoo(), 'foo init');
        $this->assertEquals($common->bar, 'bar init');
        $this->assertEquals($common->getThy(), 'thy init');

        /** All */
        $def1 = new Definition($container, 'common', Mocks\Common::class);
        $common = $resolver->make($def1);

        $this->assertInstanceOf(Mocks\Common::class, $common);
        $this->assertEquals($common->getFoo(), '');
        $this->assertEquals($common->bar, '');
        $this->assertEquals($common->getThy(), '');

        $def1->parameter('foo', $foo)
            ->property('bar', 'bar')
            ->setter('thy', $thy);

        $common = $resolver->make($def1);

        $this->assertInstanceOf(Mocks\Common::class, $common);
        $this->assertEquals($common->getFoo(), $foo);
        $this->assertEquals($common->bar, 'bar');
        $this->assertEquals($common->getThy(), $thy);
    }

    public function testFactory()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'bar', Mocks\Bar::class);
        $def->factory('SBar');
        $def->parameter('foo', 'foo')
            ->parameter('bar', 'bar');

        $bar = $resolver->make($def);
        $this->assertInstanceOf(Mocks\Bar::class, $bar);

        $this->assertEquals($bar->getFoo(), 'foo');
        $this->assertEquals($bar->getBar(), 'bar');
    }

    public function testFactoryNotExist()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'bar', Mocks\Bar::class);
        $def->factory('notFoundMethod');

        $this->initException();
        $resolver->make($def);
    }

    public function testFactoryNotStatic()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'bar', Mocks\Bar::class);
        $def->factory('getBar');

        $this->initException();
        $resolver->make($def);
    }

    public function testFactoryNotPublic()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'bar', Mocks\Bar::class);
        $def->factory('PBar');

        $this->initException();
        $resolver->make($def);
    }

    public function testFactoryException()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'foo', Mocks\Foo::class);
        $def->factory('SFoo');
        $this->initException();
        $resolver->make($def);
    }

    public function testParameters()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $foo = new Mocks\Foo();
        $foo->bar = 'foo parameter';
        $container->set('Foo', $foo);
        $thy = new Mocks\Thy();
        $thy->setFoo('thy parameter');
        $container->set('Thy', $thy);

        $container->setParameter('foo', 'Foo');
        $container->setParameter('bar', 'bar parameter');
        $container->setParameter('thy', 'Thy');

        $def = new Definition($container, 'common', Mocks\Common::class);

        $def->parameter('$foo', '%foo%');
        $def->property('$bar', '%bar%');
        $def->setter('$thy', '%thy%');

        /** @var Mocks\Common $common */
        $common = $resolver->make($def);

        $this->assertEquals($common->bar, 'bar parameter');
        $this->assertEquals($common->getFoo()->bar, 'foo parameter');
        $this->assertEquals($common->getThy()->getFoo(), 'thy parameter');
    }

    public function testMakeWithParams()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'bar', Mocks\Bar::class);

        $def->parameter('foo', 'foo')
            ->parameter('bar', 'bar');

        /** @var Mocks\Bar $bar */
        $bar = $resolver->make($def);

        $this->assertInstanceOf(Mocks\Bar::class, $bar);
        $this->assertEquals($bar->getFoo(), 'foo');
        $this->assertEquals($bar->getBar(), 'bar');
        $this->assertEquals($bar->getThy(), 'thy'); //default value

        $def = new Definition($container, 'bar', Mocks\Bar::class);

        /** @var Mocks\Bar $bar */
        $bar = $resolver->make($def, ['foo' => 'foo123', 'bar' => 'bar23']);

        $this->assertInstanceOf(Mocks\Bar::class, $bar);
        $this->assertEquals($bar->getFoo(), 'foo123');
        $this->assertEquals($bar->getBar(), 'bar23');
        $this->assertEquals($bar->getThy(), 'thy'); //default value
    }

    public function testContainer()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $def = new Definition($container, 'foo', Mocks\Foo::class);

        $def->property('foo', 'foo')
            ->property('bar', 'bar');

        /** @var Mocks\Foo $foo */
        $foo = $resolver->make($def);

        $this->assertNull($foo->getContainer());

        $def = new Definition($container, 'bar', Mocks\Bar::class);

        $def->parameter('foo', 'foo')
            ->parameter('bar', 'bar');

        /** @var Mocks\Bar $bar */
        $bar = $resolver->make($def);

        $this->assertEquals($container, $bar->getContainer());
    }

    public function testInvokeMethodNotFoundException()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $instance = new Mocks\Instance();

        $this->expectException(\suffi\di\NotFoundException::class);
        $resolver->invokeMethod($instance, 'foo');
    }

    public function testInvokeMethodException()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $foo = new Mocks\Foo();
        $foo->foo = 'bar';
        $container->set(Mocks\Foo::class, $foo);

        $instance = new Mocks\Instance();

        $this->initException();
        $resolver->invokeMethod($instance, 'not_exist_method');
    }

    public function testInvokeMethodPrivateException()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $foo = new Mocks\Foo();
        $foo->foo = 'bar';
        $container->set(Mocks\Foo::class, $foo);

        $instance = new Mocks\Instance();

        $this->initException();
        $resolver->invokeMethod($instance, 'bar');
    }

    public function testInvokeMethod()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $instance = new Mocks\Instance();

        $foo = new Mocks\Foo();
        $foo->foo = 'bar';
        $container->set(Mocks\Foo::class, $foo);

        $this->assertEquals($resolver->invokeMethod($instance, 'foo'), 'bar');

        $container->get(Mocks\Foo::class)->foo = 'foo';
        $this->assertEquals($resolver->invokeMethod($instance, 'foo'), 'foo');

        $this->assertEquals($resolver->invokeMethod($instance, 'foo', ['bar' => 'BAR']), 'fooBAR');

        $foo = new Mocks\Foo();
        $foo->foo = 'FOO';

        $this->assertEquals($resolver->invokeMethod($instance, 'foo', ['foo' => $foo, 'bar' => 'BAR']), 'FOOBAR');
    }


    public function testInvokeMethodStatic()
    {
        $container = $this->getContainer();
        $resolver = $container->getResolver();

        $instance = new Mocks\Instance();

        $foo = new Mocks\Foo();
        $foo->foo = 'bar';
        $container->set(Mocks\Foo::class, $foo);

        $this->assertEquals($resolver->invokeMethod($instance, 'sfoo'), 'bar');

        $container->get(Mocks\Foo::class)->foo = 'foo';
        $this->assertEquals($resolver->invokeMethod($instance, 'sfoo'), 'foo');

        $this->assertEquals($resolver->invokeMethod($instance, 'sfoo', ['bar' => 'BAR']), 'fooBAR');

        $foo = new Mocks\Foo();
        $foo->foo = 'FOO';

        $this->assertEquals($resolver->invokeMethod($instance, 'sfoo', ['foo' => $foo, 'bar' => 'BAR']), 'FOOBAR');
    }
}
