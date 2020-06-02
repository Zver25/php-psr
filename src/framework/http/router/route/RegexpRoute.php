<?php

namespace framework\http\router\route;

use framework\http\router\Result;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RegexpRoute implements Route
{

    /** @var string */
    private $name;
    /** @var string */
    private $pattern;
    /** @var RequestHandlerInterface */
    private $handler;
    /** @var array */
    private $tokens;
    /** @var string[] */
    private $methods;

    public function __construct(string $name, string $pattern, string $handler, array $methods = [], array $tokens = [])
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->tokens = $tokens;
        $this->methods = $methods;
    }

    public function match(ServerRequestInterface $request): ?Result
    {
        if ($this->methods && !in_array($request->getMethod(), $this->methods, true)) {
            return null;
        }
        $patterns = [];
        $replacements = [];
        foreach ($this->tokens as $key => $value) {
            $patterns[] = '/{' . $key . '}/';
            $replacements[] = "(?P<{$key}>{$value})";
        }
        $pattern = count($patterns) > 0
            ? preg_replace($patterns, $replacements, $this->pattern)
            : $this->pattern;
        if (preg_match($pattern, $request->getUri()->getPath(), $matches)) {
            $attributes = [];
            foreach ($this->tokens as $key => $_) {
                if (isset($matches[$key])) {
                    $attributes[$key] = $matches[$key];
                } else {
                    $attributes[$key] = '';
                }
            }
            return new Result($this->name, $this->handler, $attributes);
        }
        return null;
    }

}
