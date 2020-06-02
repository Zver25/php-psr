<?php

namespace framework\http\middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MemoryUsageMiddleware implements MiddlewareInterface
{

    const HEADER = 'X-Memory';
    const EXT = ['B', 'KB', 'MB', 'GB'];

    private function getMemoryUsage(): string
    {
        $i = 0;
        $memory = memory_get_peak_usage(true);
        while ($memory > 512 && $i < count(self::EXT)) {
            $memory /= 1024;
            $i++;
        }
        return sprintf('%.2f %s', $memory, self::EXT[$i]);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        return $response->withHeader(self::HEADER, $this->getMemoryUsage());
    }

}