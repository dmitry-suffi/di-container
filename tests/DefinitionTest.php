<?php

namespace suffi\di\Tests;

use \suffi\di\Container;
use \suffi\di\Definition;
use \suffi\di\Tests\Mocks;

/**
 * Class DefinitionTest
 */
class DefinitionTest extends \PHPUnit\Framework\TestCase
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

    public function testNoNameException()
    {
        $container = $this->getContainer();
        $this->initException();
        new Definition($container, '', Mocks\Foo::class);
    }

    public function testNoClassNameException()
    {
        $container = $this->getContainer();
        $this->initException();
        new Definition($container, 'foo', '');
    }

    public function testDefinition()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'foo', Mocks\Foo::class);

        $this->assertEquals($def->getName(), 'foo');
        $this->assertEquals($def->getClassName(), Mocks\Foo::class);
        $def->setClassName(Mocks\Bar::class);
        $this->assertEquals($def->getClassName(), Mocks\Bar::class);

        $container->setAlias('foo', 'bar');

        $this->assertEquals('bar', $def->getContainer()->getAlias('foo'));
    }

    public function testGetProperties()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'foo', Mocks\Foo::class);

        $this->assertEquals($def->getProperty('foo'), null);
        $this->assertEquals($def->getProperties(), []);

        $def->property('foo', 'foo')
            ->property('bar', 'bar');

        $this->assertEquals($def->getProperties(), ['foo' => 'foo', 'bar' => 'bar']);
        $this->assertEquals($def->getProperty('foo'), 'foo');
        $this->assertEquals($def->getProperty('bar'), 'bar');
    }

    public function testGetParameters()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'bar', Mocks\Bar::class);

        $this->assertEquals($def->getParameter('foo'), null);
        $this->assertEquals($def->getParameters(), []);

        $def->parameter('foo', 'foo')
            ->parameter('bar', 'bar');

        $this->assertEquals($def->getParameters(), ['foo' => 'foo', 'bar' => 'bar']);
        $this->assertEquals($def->getParameter('foo'), 'foo');
        $this->assertEquals($def->getParameter('bar'), 'bar');
    }

    public function testGetSetters()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'bar', Mocks\Bar::class);

        $this->assertEquals($def->getSetter('foo'), null);
        $this->assertEquals($def->getSetters(), []);

        $def->setter('foo', 'foo')
            ->setter('bar', 'bar');

        $this->assertEquals($def->getSetters(), ['foo' => 'foo', 'bar' => 'bar']);
        $this->assertEquals($def->getSetter('foo'), 'foo');
        $this->assertEquals($def->getSetter('bar'), 'bar');
    }

    public function testGetInit()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'bar', Mocks\Bar::class);

        $this->assertEquals($def->getInit(), '');

        $def->init('initMethod');

        $this->assertEquals($def->getInit(), 'initMethod');
    }

    public function testGetFactory()
    {
        $container = $this->getContainer();

        $def = new Definition($container, 'bar', Mocks\Bar::class);

        $this->assertEquals($def->getFactory(), '');

        $def->factory('FactoryMethod');

        $this->assertEquals($def->getFactory(), 'FactoryMethod');
    }

    public function testGetSingleton()
    {
        $container = new Container();

        $container->addDefinition('foo', Mocks\Foo::class)
            ->setSingleton(true);

        $container->addDefinition('common', Mocks\Common::class)
            ->parameter('foo', 'foo');

        $this->assertFalse($container->has('common'));
        $this->assertFalse($container->has('foo'));

        $common = $container->get('common');

        $this->assertTrue($container->has('foo'));

        $foo = $container->get('foo');
        $foo->bar = 'foo';

        $this->assertEquals($common->getFoo()->bar, $foo->bar);
    }

    public function testGetNotSingleton()
    {
        $container = new Container();

        $container->addDefinition('foo', Mocks\Foo::class)
            ->setSingleton(false);

        $container->addDefinition('common', Mocks\Common::class)
            ->parameter('foo', 'foo');

        $this->assertFalse($container->has('common'));
        $this->assertFalse($container->has('foo'));

        $common = $container->get('common');

        $this->assertFalse($container->has('foo'));
        $this->assertEquals($common->getFoo()->bar, '');
        $foo = $container->get('foo');
        $this->assertFalse($container->has('foo'));
        $this->assertEquals($common->getFoo()->bar, '');
        $foo->bar = 'foo';

        $this->assertEquals($common->getFoo()->bar, '');
    }
}
