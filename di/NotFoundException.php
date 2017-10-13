<?php

namespace suffi\di;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class Exception
 * @package suffi\di
 */
class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{

}