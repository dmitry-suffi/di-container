<?php

namespace suffi\di\Builder;

use suffi\di\Container;
use suffi\di\Definition;

/**
 * Класс для построения контейнера из массива
 * Class ArrayBuilder
 * @package suffi\di\Builder
 */
class ArrayBuilder extends Builder
{
    protected $config = [];

    /**
     * ArrayBuilder constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function build(): Container
    {
        $container = new Container();

        if (isset($this->config['services'])) {
            foreach ($this->config['services'] as $key => $item) {
                if (is_array($item)) {
                    if (isset($item['class'])) {
                        $definition = $container->addDefinition($key, $item['class']);
                        self::configureDefinition($item, $definition);
                    }
                }
            }
        }

        if (isset($this->config['aliases'])) {
            foreach ($this->config['aliases'] as $key => $name) {
                $container->setAlias($key, $name);
            }
        }

        if (isset($this->config['parameters'])) {
            foreach ($this->config['parameters'] as $key => $name) {
                $container->setParameter($key, $name);
            }
        }

        return $container;
    }

    /**
     * @inheritdoc
     */
    public function merge(Container $container)
    {
        if (isset($this->config['services'])) {
            foreach ($this->config['services'] as $key => $item) {
                if (is_array($item)) {
                    $definition = false;
                    if ($container->hasDefinition($key)) {
                        $definition = $container->getDefinition($key);
                    } else {
                        if (isset($item['class'])) {
                            $definition = $container->addDefinition($key, $item['class']);
                        }
                    }
                    if ($definition) {
                        self::configureDefinition($item, $definition);
                    }
                }
            }
        }

        if (isset($this->config['aliases'])) {
            foreach ($this->config['aliases'] as $key => $name) {
                $container->setAlias($key, $name);
            }
        }

        if (isset($this->config['parameters'])) {
            foreach ($this->config['parameters'] as $key => $name) {
                $container->setParameter($key, $name);
            }
        }
    }

    /**
     * @param $item
     * @param Definition $definition
     */
    public static function configureDefinition($item, Definition $definition)
    {
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
        if (isset($item['factory'])) {
            $definition->factory($item['factory']);
        }
        if (isset($item['singleton'])) {
            $definition->setSingleton((bool)$item['singleton']);
        }
        if (isset($item['class'])) {
            $definition->setClassName($item['class']);
        }
    }


    /**
     * Create for array
     * @param Container $container
     * @param array $config
     * @return null|object
     */
    public static function makeFromArray(Container $container, array $config)
    {
        if (isset($config['class'])) {
            $definition = new Definition($container, $config['class'], $config['class']);
            ArrayBuilder::configureDefinition($config, $definition);
            return $container->getResolver()->make($definition);
        }
        return null;
    }
}
