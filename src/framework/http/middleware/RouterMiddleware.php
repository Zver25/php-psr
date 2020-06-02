<?php

namespace framework\http\middleware;

use framework\http\router\exception\RequestNotMatchedException;
use framework\http\router\Result;
use framework\http\router\Router;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouterMiddleware implements MiddlewareInterface
{
    /** @var Router */
    private $router;
    /** @var ContainerInterface */
    private $container;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            /** @var Result $result */
            $result = $this->router->match($request);
            foreach ($result->getAttributes() as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }
            $request = $request->withAttribute(Result::class, $result);
            return $handler->handle($request);
        } catch (RequestNotMatchedException $e) {
            return new HtmlResponse('', 404);
        }
    }

}