<?php

class A {

    private function __construct()
    {
    }

}

$reflection = new ReflectionClass(A::class);

var_dump($reflection->isInstantiable());

var_dump($reflection->getConstructor());

