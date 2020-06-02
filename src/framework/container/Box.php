<?php

namespace framework\container;

use Psr\Container\ContainerInterface;

interface Box
{
    public function open(ContainerInterface $container);
}
