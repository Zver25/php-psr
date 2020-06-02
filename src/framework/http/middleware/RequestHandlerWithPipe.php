<?php

namespace framework\http\middleware;

use Laminas\Stratigility\MiddlewarePipeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerWithPipe implements RequestHandlerInterface
{

    /** @var RequestHandlerInterface */
    private $handler;
    /** @var MiddlewarePipeInterface */
    private $pipe;

    public function __construct(RequestHandlerInterface $handler, MiddlewarePipeInterface $pipe)
    {
        $this->handler = $handler;
        $this->pipe = $pipe;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->pipe->process($request, $this->handler);
    }
}