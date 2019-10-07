<?php

namespace suffi\di\Builder;

use suffi\di\Container;

/**
 * Класс для построения контейнера
 * Class Builder
 * @package suffi\di\Builder
 */
abstract class Builder
{

    /**
     * Create Container
     * @return Container
     */
    abstract public function build():Container;

    /**
     * Add config in container
     * @param Container $container
     */
    abstract public function merge(Container $container);
}
