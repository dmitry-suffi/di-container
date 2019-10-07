<?php

namespace suffi\di\Tests;

use suffi\di\Builder\ArrayBuilder;
use suffi\di\Container;
use suffi\di\Definition;
use suffi\di\Tests\Mocks\Common;
use suffi\di\Tests\Mocks\Foo;
use suffi\di\Tests\Mocks\Thy;
use PHPUnit\Framework\TestCase;

class ArrayBuilderTest extends TestCase
{

    public function testEmptyBuild()
    {
        $containerBuilder = new ArrayBuilder([]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $property = new \ReflectionProperty($container, 'definitions');
        $property->setAccessible(true);
        $this->assertEquals([], $property->getValue($container));

        $property = new \ReflectionProperty($container, 'aliases');
        $property->setAccessible(true);
        $this->assertEquals([], $property->getValue($container));
    }

    public function testBuildParams()
    {
        $containerBuilder = new ArrayBuilder([
            'services' => [
                'common' => [
                    'class' => Common::class,
                    'parameters' => [
                        'foo' => 'foo'
                    ],
                    'properties' => [
                        'bar' => 'foobar'
                    ],
                    'setters' => [
                        'thy' => 'thy'
                    ]
                ],
                'foo' => [
                    'class' => Foo::class,
                    'properties' => [
                        'foo' => '123456'
                    ],
                ],
                'thy' => [
                    'class' => Thy::class,
                    'setters' => [
                        'foo' => '987654'
                    ],
                ]
            ]
        ]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $definition = $container->getDefinition('common');
        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertEquals($definition->getParameter('foo'), 'foo');
        $this->assertEquals($definition->getParameters(), ['foo' => 'foo']);
        $this->assertEquals($definition->getSetter('thy'), 'thy');
        $this->assertEquals($definition->getSetters(), ['thy' => 'thy']);
        $this->assertEquals($definition->getProperty('bar'), 'foobar');
        $this->assertEquals($definition->getProperties(), ['bar' => 'foobar']);

        $this->assertTrue($container->hasDefinition('common'));
        $common = $container->get('common');
        $this->assertInstanceOf(Common::class, $common);
        $this->assertEquals($common->bar, 'foobar');
        $this->assertEquals($common->getFoo()->foo, '123456');
        $this->assertEquals($common->getThy()->getFoo(), '987654');
    }

    public function testBuildInit()
    {
        $containerBuilder = new ArrayBuilder([
            'services' => [
                'common' => [
                    'class' => Common::class,
                    'init' => 'initAll'
                ],
                'foo' => [
                    'class' => Foo::class,
                    'properties' => [
                        'foo' => '123456'
                    ],
                ],
                'thy' => [
                    'class' => Thy::class,
                    'setters' => [
                        'foo' => '987654'
                    ],
                ]
            ]
        ]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $definition = $container->getDefinition('common');
        $this->assertInstanceOf(Definition::class, $definition);
        $this->assertEquals($definition->getInit(), 'initAll');

        $this->assertTrue($container->hasDefinition('common'));
        $common = $container->get('common');
        $this->assertInstanceOf(Common::class, $common);
        $this->assertEquals($common->bar, 'bar init');
        $this->assertEquals($common->getFoo(), 'foo init');
        $this->assertEquals($common->getThy(), 'thy init');
    }

    public function testBuildFactory()
    {
        $containerBuilder = new ArrayBuilder([
            'services' => [
                'common' => [
                    'class' => Common::class,
                    'factory' => 'SBCommon',
                    'parameters' => [
                        'foo' => 'foo'
                    ],
                    'setters' => [
                        'thy' => 'thy'
                    ]
                ],
                'foo' => [
                    'class' => Foo::class,
                    'properties' => [
                        'foo' => '123456'
                    ],
                ],
                'thy' => [
                    'class' => Thy::class,
                    'setters' => [
                        'foo' => '987654'
                    ],
                ]
            ]
        ]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $definition = $container->getDefinition('common');
        $this->assertInstanceOf(Definition::class, $definition);
        $this->assertEquals($definition->getFactory(), 'SBCommon');

        $this->assertTrue($container->hasDefinition('common'));
        $common = $container->get('common');
        $this->assertInstanceOf(Common::class, $common);
        $this->assertEquals($common->bar, 'factory bar');
        $this->assertEquals($common->getFoo()->foo, '123456');
        $this->assertEquals($common->getThy()->getFoo(), '987654');
    }

    public function testBuildsingletone()
    {
        $containerBuilder = new ArrayBuilder([
            'services' => [
                'foo' => [
                    'class' => Foo::class,
                    'singleton' => true,
                    'properties' => [
                        'foo' => '123456'
                    ],
                ],
                'thy' => [
                    'class' => Thy::class,
                    'singleton' => false,
                    'setters' => [
                        'foo' => '987654'
                    ],
                ]
            ]
        ]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $definition = $container->getDefinition('foo');
        $this->assertInstanceOf(Definition::class, $definition);
        $this->assertTrue($definition->isSingleton());

        $definition = $container->getDefinition('thy');
        $this->assertInstanceOf(Definition::class, $definition);
        $this->assertFalse($definition->isSingleton());
    }

    public function testBuildAlias()
    {
        $containerBuilder = new ArrayBuilder([
            'aliases' => [
                'Mail' => 'vendor/mail',
                'DB' => 'vendor/db'
            ]
        ]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $this->assertEquals('vendor/mail', $container->getAlias('Mail'));
        $this->assertEquals('vendor/db', $container->getAlias('DB'));
    }

    public function testBuildParameters()
    {
        $containerBuilder = new ArrayBuilder([
            'parameters' => [
                'email' => 'test@test.ru',
                'is_send' => 1
            ]
        ]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $this->assertEquals('test@test.ru', $container->getParameter('email'));
        $this->assertEquals(1, $container->getParameter('is_send'));
    }

    public function testMergeParams()
    {
        $containerBuilder = new ArrayBuilder([
            'services' => [
                'common' => [
                    'class' => Common::class,
                    'parameters' => [
                        'foo' => 'foo'
                    ],
                    'properties' => [
                        'bar' => 'foobar'
                    ],
                ],
                'foo' => [
                    'class' => Foo::class,
                    'properties' => [
                        'foo' => '123456'
                    ],
                ],
            ]
        ]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $this->assertTrue($container->hasDefinition('common'));
        $this->assertFalse($container->hasDefinition('thy'));
        $this->assertTrue($container->hasDefinition('foo'));

        $common = $container->get('common');
        $this->assertInstanceOf(Common::class, $common);
        $this->assertEquals($common->bar, 'foobar');
        $this->assertEquals($common->getFoo()->foo, '123456');
        $this->assertEquals($common->getThy(), null);

        $definition = $container->getDefinition('common');
        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertEquals($definition->getSetter('thy'), null);
        $this->assertEquals($definition->getSetters(), []);

        $containerBuilder = new ArrayBuilder([
            'services' => [
                'common' => [
                    'setters' => [
                        'thy' => 'thy'
                    ]
                ],
                'thy' => [
                    'class' => Thy::class,
                    'setters' => [
                        'foo' => '987654'
                    ],
                ]
            ]
        ]);

        $container->remove('common');

        $containerBuilder->merge($container);

        $this->assertTrue($container->hasDefinition('common'));
        $this->assertTrue($container->hasDefinition('thy'));
        $this->assertTrue($container->hasDefinition('foo'));

        $definition = $container->getDefinition('common');
        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertEquals($definition->getSetter('thy'), 'thy');
        $this->assertEquals($definition->getSetters(), ['thy' => 'thy']);

        $common = $container->get('common');
        $this->assertInstanceOf(Common::class, $common);
        $this->assertEquals($common->bar, 'foobar');
        $this->assertEquals($common->getFoo()->foo, '123456');
        $this->assertEquals($common->getThy()->getFoo(), '987654');
    }

    public function testMergeAllParams()
    {
        $containerBuilder = new ArrayBuilder([
            'services' => [
                'common' => [
                    'class' => Common::class,
                ],
                'foo' => [
                    'class' => Foo::class,
                    'properties' => [
                        'foo' => '123456'
                    ],
                ],
                'thy' => [
                    'class' => Thy::class,
                    'setters' => [
                        'foo' => '987654'
                    ],
                ]
            ]
        ]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $this->assertTrue($container->hasDefinition('common'));

        $definition = $container->getDefinition('common');
        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertEquals($definition->getSetter('thy'), null);
        $this->assertEquals($definition->getSetters(), []);
        $this->assertEquals($definition->getFactory(), '');
        $this->assertEquals($definition->getInit(), '');

        $containerBuilder = new ArrayBuilder([
            'services' => [
                'common' => [
                    'factory' => 'SBCommon',
                    'init' => 'initAll',
                    'parameters' => [
                        'foo' => 'foo'
                    ],
                    'properties' => [
                        'bar' => 'foobar'
                    ],
                    'setters' => [
                        'thy' => 'thy'
                    ]
                ],
            ]
        ]);

        $container->remove('common');

        $containerBuilder->merge($container);

        $this->assertTrue($container->hasDefinition('common'));
        $this->assertTrue($container->hasDefinition('thy'));
        $this->assertTrue($container->hasDefinition('foo'));

        $definition = $container->getDefinition('common');
        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertEquals($definition->getSetter('thy'), 'thy');
        $this->assertEquals($definition->getSetters(), ['thy' => 'thy']);
        $this->assertEquals($definition->getFactory(), 'SBCommon');
        $this->assertEquals($definition->getInit(), 'initAll');

        $common = $container->get('common');
        $this->assertInstanceOf(Common::class, $common);
        $this->assertEquals($common->bar, 'bar init');
        $this->assertEquals($common->getFoo(), 'foo init');
        $this->assertEquals($common->getThy(), 'thy init');
    }

    public function testMergeAlias()
    {
        $containerBuilder = new ArrayBuilder([
            'aliases' => [
                'Mail' => 'vendor/mail',
                'DB' => 'vendor/db'
            ]
        ]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $this->assertEquals('vendor/mail', $container->getAlias('Mail'));
        $this->assertEquals('vendor/db', $container->getAlias('DB'));
        $this->assertFalse($container->getAlias('Sms'));

        $containerBuilder = new ArrayBuilder([
            'aliases' => [
                'Mail' => 'vendor-new/mail',
                'Sms' => 'vendor-new/sms'
            ]
        ]);

        $containerBuilder->merge($container);

        $this->assertEquals('vendor-new/mail', $container->getAlias('Mail'));
        $this->assertEquals('vendor/db', $container->getAlias('DB'));
        $this->assertEquals('vendor-new/sms', $container->getAlias('Sms'));
    }

    public function testMergeParameters()
    {
        $containerBuilder = new ArrayBuilder([
            'parameters' => [
                'email' => 'test@test.ru',
                'is_send' => 1
            ]
        ]);

        $container = $containerBuilder->build();
        $this->assertInstanceOf(Container::class, $container);

        $this->assertEquals('test@test.ru', $container->getParameter('email'));
        $this->assertEquals(1, $container->getParameter('is_send'));
        $this->assertFalse($container->getParameter('subject'));

        $containerBuilder = new ArrayBuilder([
            'parameters' => [
                'Mail' => 'vendor-new/mail',
                'is_send' => 0,
                'subject' => 'test mail'
            ]
        ]);

        $containerBuilder->merge($container);

        $this->assertEquals('test@test.ru', $container->getParameter('email'));
        $this->assertEquals(0, $container->getParameter('is_send'));
        $this->assertEquals('test mail', $container->getParameter('subject'));
    }

    public function testMakeFromArray()
    {
        $container = new Container();

        $config = [
            'class' => Mocks\Bar::class,
            'parameters' => [
                'foo' => 'foo123',
                'bar' => 'bar23'
            ]
        ];

        /** @var Mocks\Bar $bar */
        $bar = ArrayBuilder::makeFromArray($container, $config);

        $this->assertInstanceOf(Mocks\Bar::class, $bar);
        $this->assertEquals($bar->getFoo(), 'foo123');
        $this->assertEquals($bar->getBar(), 'bar23');
        $this->assertEquals($bar->getThy(), 'thy'); //default value

        $this->assertNull(ArrayBuilder::makeFromArray($container, []));
    }
}
