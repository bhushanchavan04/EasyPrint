<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $groupMiddleware = [];

    public function get(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function group(array $attributes, callable $callback): void
    {
        $previousMiddleware = $this->groupMiddleware;
        if (isset($attributes['middleware'])) {
            $middlewares = is_array($attributes['middleware']) ? $attributes['middleware'] : [$attributes['middleware']];
            $this->groupMiddleware = array_merge($this->groupMiddleware, $middlewares);
        }

        $callback($this);

        $this->groupMiddleware = $previousMiddleware;
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middleware = []): self
    {
        $mergedMiddleware = array_merge($this->groupMiddleware, $middleware);
        $this->routes[] = [
            'method' => $method,
            'path' => '/' . trim($path, '/'),
            'handler' => $handler,
            'middleware' => $mergedMiddleware,
        ];
        return $this;
    }

    public function dispatch(Request $request, Response $response): void
    {
        $reqMethod = $request->getMethod();
        $reqUri = $request->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $reqMethod) {
                continue;
            }

            $pattern = $this->compileRoutePattern($route['path']);
            if (preg_match($pattern, $reqUri, $matches)) {
                // Extract named matches
                $params = array_filter($matches, fn($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY);

                // Run Middleware Pipeline
                foreach ($route['middleware'] as $mw) {
                    $mwInstance = is_string($mw) ? new $mw() : $mw;
                    $handled = $mwInstance->handle($request, $response, $params);
                    if ($handled === false) {
                        return; // Middleware aborted the chain (e.g. redirected or responded)
                    }
                }

                // Dispatch handler
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    call_user_func_array($handler, [$request, $response, $params]);
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$class, $action] = $handler;
                    $controller = new $class();
                    $controller->$action($request, $response, $params);
                    return;
                }
            }
        }

        // 404 Not Found
        if ($request->isAjax() || str_starts_with($reqUri, '/api/')) {
            $response->json(['error' => 'Endpoint Not Found', 'path' => $reqUri], 404);
        } else {
            $response->setStatusCode(404);
            echo "<!DOCTYPE html><html><head><title>404 Not Found</title><link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'></head><body class='bg-gray-100 flex items-center justify-center h-screen'><div class='text-center p-8 bg-white shadow-xl rounded-xl'><h1 class='text-6xl font-bold text-gray-800 mb-4'>404</h1><p class='text-gray-600 mb-6'>Page Not Found</p><a href='/' class='bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition'>Return Home</a></div></body></html>";
        }
    }

    private function compileRoutePattern(string $routePath): string
    {
        // Replace {param} with named regex capture groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        return '#^' . $pattern . '$#';
    }
}

