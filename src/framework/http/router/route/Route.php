<?php

namespace framework\http\router\route;

use framework\http\router\Result;
use Psr\Http\Message\ServerRequestInterface;

interface Route
{
    public function match(ServerRequestInterface $request): ?Result;
}
