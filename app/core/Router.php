<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(string $uri, string $requestMethod): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        $basePath = '/nmsctf/public';
        if (str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        if ($path === '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $path) {
                [$controller, $method] = $route['handler'];

                $instance = new $controller();
                $instance->$method();
                return;
            }
        }
        echo 'PATH = ' . $path;
        http_response_code(404);
        echo "404 - Page not found";
    }
}