<?php

namespace app;

class SimpleAuth implements Auth
{
    public function auth(string $login, string $password): bool
    {
        return $login === 'admin' && $password === 'password';
    }

}