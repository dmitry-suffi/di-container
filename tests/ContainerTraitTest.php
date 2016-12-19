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
            'foo' => [
                'class' => 'Foo',
                'properties' => [
                    'bar' => 'thy'
                ]
            ]
        ];

        $c->configure($config);

        $this->assertTrue($c->getContainer()->hasDefinition('foo'));
        $foo = $c->getContainer()->get('foo');
        $this->assertInstanceOf(Foo::class, $foo);

        $this->assertEquals($foo->bar, 'thy');
    }
}
