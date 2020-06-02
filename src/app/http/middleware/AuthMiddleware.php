<?php

namespace app\http\middleware;

use app\Auth;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    /** @var Auth */
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $login = $request->getServerParams()['PHP_AUTH_USER'] ?? '';
        $password = $request->getServerParams()['PHP_AUTH_PW'] ?? '';
        if ($this->auth->auth($login, $password)) {
            return $handler->handle($request);
        }
        return new EmptyResponse(401, ['WWW-Authenticate' => 'Basic realm=Restricted area']);
    }

}