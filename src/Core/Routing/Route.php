<?php

namespace Hertz\ProductService\Core\Routing;

class Route
{
    private string $path;
    private string $method;
    private string $controller;
    private bool $isStatic;
    private array $params;

    public function __construct(
        string $path,
        string $method,
        string $controller,
    ) {
        $this->path = $path;
        $this->method = $method;
        $this->controller = $controller;
        $this->params = [];
        $this->isStatic = !str_contains($path, '{') && !str_contains($path, '}');
    }

    public static function __set_state(array $state): self
    {
        $route = new self($state['path'], $state['method'], $state['controller']);
        $route->params = $state['params'];
        $route->isStatic = $state['isStatic'];
        return $route;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
        $this->isStatic = !str_contains($path, '{') && !str_contains($path, '}');
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }


    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function addParam(string $name, mixed $value): void
    {
        $this->params[$name] = $value;
    }
}