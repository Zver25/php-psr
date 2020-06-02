<?php

namespace framework\container;

use Psr\Container\ContainerExceptionInterface;

class CantCreateItemException extends \Exception implements ContainerExceptionInterface
{
}
