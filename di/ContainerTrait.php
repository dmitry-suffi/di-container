<?php

namespace suffi\di;

trait ContainerTrait
{
    /**
     * @var ExtendedContainer
     */
    private $_container = null;

    /**
     * @return array
     */
    protected function getAliases()
    {
        return $this->_aliases ?? [];
    }

    /**
     * @return ExtendedContainer
     */
    public function getContainer()
    {
        if (is_null($this->_container)) {
            $this->_container = new ExtendedContainer();
        }
        return $this->_container;
    }

    /**
     * @param array $config
     * example
     *          [
     *              'module' => [
     *                       'class' => 'Module',
     *                       'parameters' => [
     *                            'name1' => 'value'
     *                        ],
     *                       'properties' => [
     *                            'name2' => 'value'
     *                        ],
     *                       'setters' => [
     *                            'name3' => 'value'
     *                        ],
     *                       'init' => 'init'
     *                  ]
     *          ]
     */
    public function configure(array $config)
    {
        $container = $this->getContainer();

        foreach ($config as $key => $item) {
            if (is_array($item)) {
                if (isset($item['class'])) {
                    $definition = $container->setDefinition($key, $item['class']);

                    if (isset($item['parameters']) && is_array($item['parameters'])) {
                        foreach ($item['parameters'] as $paramName => $paramValue) {
                            $definition->parameter($paramName, $paramValue);
                        }
                    }
                    if (isset($item['properties']) && is_array($item['properties'])) {
                        foreach ($item['properties'] as $paramName => $paramValue) {
                            $definition->property($paramName, $paramValue);
                        }
                    }
                    if (isset($item['setters']) && is_array($item['setters'])) {
                        foreach ($item['setters'] as $paramName => $paramValue) {
                            $definition->setter($paramName, $paramValue);
                        }
                    }
                    if (isset($item['init'])) {
                        $definition->init($item['init']);
                    }
                }
            }
        }

        foreach ($this->getAliases() as $key => $name) {
            $container->setAlias($key, $name);
        }
    }

}