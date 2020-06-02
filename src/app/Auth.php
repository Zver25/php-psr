<?php

namespace app;

interface Auth
{
    public function auth(string $login, string $password): bool;
}
