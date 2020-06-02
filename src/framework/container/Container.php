<?php

namespace framework\container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{

    /** @var Container */
    private static $instance;
    private $definitions = [];
    private $results = []; // parsed value

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws CantCreateItemException
     * @throws \ReflectionException
     */
    public function get($id)
    {
        if ($this->has($id)) {
            if (!array_key_exists($id, $this->results)) {
                $this->parse($id);
            }
            return $this->results[$id];
        }
        throw new NotFoundItemException();
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id): bool
    {
        return array_key_exists($id, $this->definitions) || class_exists($id);
    }

    public function set($id, $value): void
    {
        $this->definitions[$id] = $value;
        unset($this->results[$id]);
    }

    /**
     * @param string $id
     * @throws \ReflectionException
     * @throws CantCreateItemException
     */
    private function parse($id): void
    {
        if ($this->definitions[$id] instanceof Box) {
            $this->results[$id] = $this->definitions[$id]->open($this);
        } else if (class_exists($id)) {
            $this->results[$id] = $this->createObject($id);
        } else {
            $this->results[$id] = $this->definitions[$id];
        }
    }

    /**
     * @param $className
     * @return object
     * @throws \ReflectionException
     * @throws CantCreateItemException
     */
    private function createObject($className)
    {
        $reflection = new \ReflectionClass($className);
        $arguments = [];
        if ($reflection->isInstantiable()) {
            if (($constructor = $reflection->getConstructor()) === null) {
                return $reflection->newInstance();
            }
            else {
                foreach ($constructor->getParameters() as $param) {
                    if ($param->isOptional()) {
                        break;
                    }
                    if ($this->has($param->getClass()->getName())) {
                        $arguments[] = $this->get($param->getClass()->getName());
                    }
                    else {
                        throw new CantCreateItemException();
                    }
                }
                return $reflection->newInstanceArgs($arguments);
            }
        }
        throw new CantCreateItemException();
    }
}
