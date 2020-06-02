<?php

namespace app\http\action;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NotFoundAction implements RequestHandlerInterface
{

    public function __construct()
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(404);
    }
}
