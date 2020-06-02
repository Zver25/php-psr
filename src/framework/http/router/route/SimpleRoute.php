<?php


namespace framework\http\router\route;


use framework\http\router\Action;
use framework\http\router\Result;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SimpleRoute implements Route
{
    /** @var string */
    private $name;
    /** @var string */
    private $path;
    /** @var string */
    private $handler;
    /** @var string[] */
    private $methods;

    /**
     * SimpleRoute constructor.
     * @param string $name
     * @param string $path
     * @param string $handler
     * @param string[] $methods
     */
    public function __construct(string $name, string $path, string $handler, array $methods = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->handler = $handler;
        $this->methods = $methods;
    }


    public function match(ServerRequestInterface $request): ?Result
    {
        if ($this->methods && !in_array($request->getMethod(), $this->methods, true)) {
            return null;
        }
        if ($request->getUri()->getPath() === $this->path) {
            return new Result($this->name, $this->handler, []);
        }
        return null;
    }
}
