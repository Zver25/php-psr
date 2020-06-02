<?php

namespace framework\http\middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponseTimeMiddleware implements MiddlewareInterface
{
    const HEADER = 'X-Response-Time';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $time = -microtime(true);
        /** @var ResponseInterface $response */
        $response = $handler->handle($request);
        $time += microtime(true);

        return $response->withHeader(self::HEADER, sprintf('%2.3fms', $time * 1000));
    }

}
