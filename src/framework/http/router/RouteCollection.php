<?php

namespace framework\http\router;

use framework\http\router\route\Route;

class RouteCollection
{
    /** @var Route[] */
    private $routes = [];

    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

}