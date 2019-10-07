<?php

namespace suffi\di\Tests;

use suffi\di\Container;
use suffi\di\ContainerTrait;
use suffi\di\ExtendedContainer;
use suffi\di\Tests\Mocks\Common;
use suffi\di\Tests\Mocks\Foo;
use suffi\di\Tests\Mocks\Thy;

class ContainerTraitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return ContainerTrait
     */
    protected function getContainer()
    {
        return new class()
        {
            use ContainerTrait;
        };
    }

    public function testTrait()
    {
        $containerObject = $this->getContainer();
        $this->assertInstanceOf(Container::class, $containerObject->getContainer());
        $this->assertEquals($containerObject->getContainer()->getAlias('alias'), '');

        $container = new ExtendedContainer();
        $container->setAlias('alias', '123456');

        $containerObject->setContainer($container);
        $this->assertInstanceOf(Container::class, $containerObject->getContainer());
        $this->assertEquals($containerObject->getContainer()->getAlias('alias'), '123456');
    }
}
