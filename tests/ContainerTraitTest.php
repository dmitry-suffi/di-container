<?php

use \suffi\di\ContainerTrait;

/**
 * Class ContainerTraitTest
 */
class ContainerTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return ContainerTrait 
     */
    protected function getContainer()
    {
        return new class() {
            use ContainerTrait;
        };
    }

    public function testConfig()
    {
        $c = $this->getContainer();

        $this->assertInstanceOf(\suffi\di\Container::class, $c->getContainer());

        $config = [
            'common' => [
                'class' => 'Common',
                'parameters' => [
                    'foo' => new Foo()
                ],
                'properties' => [
                    'bar' => 'foobar'
                ],
                'setters' => [
                    'thy' => new Thy()
                ]
            ]
        ];

        $c->configure($config);

        $this->assertTrue($c->getContainer()->hasDefinition('common'));
        $common = $c->getContainer()->get('common');
        $this->assertInstanceOf(Common::class, $common);

        $this->assertEquals($common->bar, 'foobar');
    }

    public function testDefConfig()
    {
        $c = $this->getContainer();

        $this->assertInstanceOf(\suffi\di\Container::class, $c->getContainer());

        $config = [
            'common' => [
                'class' => 'Common',
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
                'class' => 'Foo',
                'properties' => [
                    'foo' => '123456'
                ],
            ],
            'thy' => [
                'class' => 'Thy',
                'setters' => [
                    'foo' => '987654'
                ],
            ]
        ];

        $c->configure($config);

        $this->assertTrue($c->getContainer()->hasDefinition('common'));
        $common = $c->getContainer()->get('common');
        $this->assertInstanceOf(Common::class, $common);

        $this->assertEquals($common->bar, 'foobar');
        $this->assertEquals($common->getFoo()->foo, '123456');
        $this->assertEquals($common->getThy()->getFoo(), '987654');
    }

    public function testInitConfig()
    {
        $c = $this->getContainer();

        $this->assertInstanceOf(\suffi\di\Container::class, $c->getContainer());

        $config = [
            'common' => [
                'class' => 'Common',
                'init' => 'initAll'
            ]
        ];

        $c->configure($config);

        $this->assertTrue($c->getContainer()->hasDefinition('common'));
        $common = $c->getContainer()->get('common');
        $this->assertInstanceOf(Common::class, $common);

        $this->assertEquals($common->getFoo(), 'foo init');
        $this->assertEquals($common->bar, 'bar init');
        $this->assertEquals($common->getThy(), 'thy init');
    }
}
