<?php

namespace suffi\di;

/**
 * Class ExtendedContainer
 * @package suffi\di
 */
class ExtendedContainer extends Container
{

    /**
     * @var Container
     */
    protected $parentsContainer = null;

    /**
     * @return Container
     */
    public function getParentsContainer()
    {
        return $this->parentsContainer;
    }

    /**
     * @param Container $parentsContainer
     */
    public function setParentsContainer(Container $parentsContainer)
    {
        $this->parentsContainer = $parentsContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        try {
            $obj = parent::get($key);
            return $obj;
        } catch (NotFoundException $e) {
            if (!is_null($this->parentsContainer)) {
                return $this->parentsContainer->get($key);
            } else {
                throw $e;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $has = parent::has($key);

        if (!$has && !is_null($this->parentsContainer)) {
            $has = $this->parentsContainer->has($key);
        }
        return $has;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(string $key)
    {
        $obj = parent::getDefinition($key);
        if (!$obj && !is_null($this->parentsContainer)) {
            $obj = $this->parentsContainer->getDefinition($key);
        }
        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefinition(string $key)
    {
        $has = parent::hasDefinition($key);

        if (!$has && !is_null($this->parentsContainer)) {
            $has = $this->parentsContainer->hasDefinition($key);
        }
        return $has;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSingleton(string $key)
    {
        $has = parent::hasSingleton($key);
        if (!$has && !is_null($this->parentsContainer)) {
            $has = $this->parentsContainer->hasSingleton($key);
        }
        return $has;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter(string $name)
    {
        $parameter = parent::getParameter($name);

        if (!$parameter && !parent::hasParameter($name) && !is_null($this->parentsContainer)) {
            $parameter = $this->parentsContainer->getParameter($name);
        }
        return $parameter;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter(string $name)
    {
        $has = parent::hasParameter($name);
        if (!$has && !is_null($this->parentsContainer)) {
            $has = $this->parentsContainer->hasParameter($name);
        }
        return $has;
    }
}
