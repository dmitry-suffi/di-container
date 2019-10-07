<?php

namespace suffi\di;

trait ContainerTrait
{
    /**
     * @var ExtendedContainer
     */
    protected $container = null;

    /**
     * @param ExtendedContainer $container
     */
    public function setContainer(ExtendedContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @return ExtendedContainer
     */
    public function getContainer()
    {
        if (is_null($this->container)) {
            $this->container = new ExtendedContainer();
        }
        return $this->container;
    }
}
