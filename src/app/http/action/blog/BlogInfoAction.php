<?php

namespace app\http\action\blog;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BlogInfoAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        if ($id > 0) {
            return (new HtmlResponse("Post #{$id}!"));
        }
        throw new \Exception('Blog not found!');
    }
}