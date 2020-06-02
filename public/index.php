<?php

use app\Auth;
use app\http\action\AboutAction;
use app\http\action\blog\BlogIndexAction;
use app\http\action\blog\BlogInfoAction;
use app\http\action\Cabinet;
use app\http\action\NotFoundAction;
use app\http\action\RootAction;
use app\http\middleware\AuthMiddleware;
use app\http\middleware\ExceptionMiddleware;
use app\SimpleAuth;
use framework\container\Box as ContainerBox;
use framework\container\Container;
use framework\http\middleware\DispatchMiddleware;
use framework\http\middleware\MemoryUsageMiddleware;
use framework\http\middleware\RequestHandlerWithPipe;
use framework\http\middleware\ResponseTimeMiddleware;
use framework\http\middleware\RouterMiddleware;
use framework\http\router\route\RegexpRoute;
use framework\http\router\route\SimpleRoute;
use framework\http\router\RouteCollection;
use framework\http\router\Router;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\Stratigility\MiddlewarePipe;
use Psr\Container\ContainerInterface;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

### Initialization

$container = Container::getInstance();

$container->set(Auth::class, new class implements ContainerBox {
    public function open(ContainerInterface $container)
    {
        return new SimpleAuth();
    }
});

$container->set('AuthPipe', new class implements ContainerBox {
    public function open(ContainerInterface $container)
    {
        $authMiddleware = $container->get(AuthMiddleware::class);
        $authPipe = new MiddlewarePipe();
        $authPipe->pipe($authMiddleware);
        return $authPipe;
    }
});
$container->set('CabinetWithAuth', new class implements ContainerBox {
    public function open(ContainerInterface $container)
    {
        $cabinet = $container->get(Cabinet::class);
        $authPipe = $container->get('AuthPipe');
        return new RequestHandlerWithPipe($cabinet, $authPipe);
    }
});

$container->set(RouteCollection::class, new class implements ContainerBox {
    public function open(ContainerInterface $container)
    {
        $routes = new RouteCollection();
        $routes->addRoute(new SimpleRoute('root', '/', RootAction::class));
        $routes->addRoute(new SimpleRoute('about', '/about', AboutAction::class, ['GET']));
        $routes->addRoute(new SimpleRoute('blog-index', '/blog', BlogIndexAction::class, ['GET']));
        $routes->addRoute(new RegexpRoute('blog-info', '#/blog/{id}#', BlogInfoAction::class, ['GET'], ['id' => '\d+']));
        $routes->addRoute(new SimpleRoute('cabinet', '/cabinet', 'CabinetWithAuth'));
        return $routes;
    }
});

$container->set('Application', new class implements ContainerBox {
    public function open(ContainerInterface $container)
    {
        $app = new MiddlewarePipe();
        $router = $container->get(Router::class);
        $app->pipe(new ResponseTimeMiddleware());
        $app->pipe(new ExceptionMiddleware());
        $app->pipe(new MemoryUsageMiddleware());
        $app->pipe(new RouterMiddleware($router));
        $app->pipe(new DispatchMiddleware($container));
        return $app;
    }
});

### Application

$request = ServerRequestFactory::fromGlobals();

/** @var MiddlewarePipe $app */
$app = $container->get('Application');
/** @var NotFoundAction $notFoundAction */
$notFoundAction = $container->get(NotFoundAction::class);

$response = $app->process($request, $notFoundAction);

### Sending

$emitter = new SapiEmitter();
$emitter->emit($response);
