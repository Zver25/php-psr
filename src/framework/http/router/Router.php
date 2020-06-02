<?php

namespace framework\http\router;

use framework\http\router\exception\RequestNotMatchedException;
use Psr\Http\Message\ServerRequestInterface;

class Router
{

    /** @var RouteCollection */
    private $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Result
     * @throws RequestNotMatchedException
     */
    public function match(ServerRequestInterface $request): Result
    {
        foreach ($this->routes->getRoutes() as $route) {
            if ($result = $route->match($request)) {
                return $result;
            }
        }
        throw new RequestNotMatchedException();
    }

}