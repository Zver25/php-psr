<?php

namespace framework\http\middleware;

use app\http\action\NotFoundAction;
use framework\http\router\Result;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatchMiddleware implements MiddlewareInterface
{

    /** @var ContainerInterface */
    private $container;

    /**
     * DispatchMiddleware constructor.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws NotFoundAction
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Result $result */
        if ($result = $request->getAttribute(Result::class)) {
            if (($handler = $this->container->get($result->getHandler())) instanceof RequestHandlerInterface) {
                return $handler->handle($request);
            }
        }
        return $handler->handle($request);
    }

}