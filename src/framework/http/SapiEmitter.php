<?php

namespace framework\http;

use Psr\Http\Message\ResponseInterface;

class SapiEmitter
{

    public function emit(ResponseInterface $response): void {
        foreach ($response->getHeaders() as $name => $value) {
            header($name . ': '. implode(',', $value));
        }
        echo $response->getBody();
    }
}