<?php

class Router
{
    private $routes = [];
    private $middlewares = [];
    private $currentGroup = '';

    /**
     * Add route
     */
    public function add($method, $path, $handler, $middleware = [])
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $this->currentGroup . $path,
            'handler' => $handler,
            'middleware' => array_merge($this->middlewares, $middleware)
        ];
    }

    /**
     * Add GET route
     */
    public function get($path, $handler, $middleware = [])
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    /**
     * Add POST route
     */
    public function post($path, $handler, $middleware = [])
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    /**
     * Add route group with prefix
     */
    public function group($prefix, $callback, $middleware = [])
    {
        $oldGroup = $this->currentGroup;
        $oldMiddlewares = $this->middlewares;

        $this->currentGroup = $oldGroup . $prefix;
        $this->middlewares = array_merge($oldMiddlewares, $middleware);

        $callback($this);

        $this->currentGroup = $oldGroup;
        $this->middlewares = $oldMiddlewares;
    }

    /**
     * Dispatch request
     */
    public function dispatch($method, $uri)
    {
        $method = strtoupper($method);
        $uri = $this->normalizeUri($uri);

        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $method, $uri)) {
                return $this->executeRoute($route, $uri);
            }
        }

        // 404 Not Found
        $this->handleNotFound();
    }

    /**
     * Match route
     */
    private function matchRoute($route, $method, $uri)
    {
        if ($route['method'] !== $method) {
            return false;
        }

        $pattern = $this->convertToRegex($route['path']);
        return preg_match($pattern, $uri);
    }

    /**
     * Convert route path to regex
     */
    private function convertToRegex($path)
    {
        // Escape forward slashes
        $pattern = str_replace('/', '\/', $path);
        
        // Convert parameters {param} to named groups
        $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^\/]+)', $pattern);
        
        // Add start and end anchors
        $pattern = '/^' . $pattern . '$/';
        
        return $pattern;
    }

    /**
     * Execute route
     */
    private function executeRoute($route, $uri)
    {
        // Extract parameters
        $params = $this->extractParams($route['path'], $uri);

        // Execute middlewares
        foreach ($route['middleware'] as $middleware) {
            $this->executeMiddleware($middleware, $params);
        }

        // Execute handler
        return $this->executeHandler($route['handler'], $params);
    }

    /**
     * Extract parameters from URI
     */
    private function extractParams($path, $uri)
    {
        $params = [];
        $pathParts = explode('/', trim($path, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        foreach ($pathParts as $index => $part) {
            if (preg_match('/\{([^}]+)\}/', $part, $matches)) {
                $paramName = $matches[1];
                $params[$paramName] = $uriParts[$index] ?? null;
            }
        }

        return $params;
    }

    /**
     * Execute middleware
     */
    private function executeMiddleware($middleware, $params)
    {
        if (is_string($middleware)) {
            $middlewareClass = $middleware;
            if (class_exists($middlewareClass)) {
                $middlewareInstance = new $middlewareClass();
                if (method_exists($middlewareInstance, 'handle')) {
                    $middlewareInstance->handle($params);
                }
            }
        } elseif (is_callable($middleware)) {
            call_user_func($middleware, $params);
        }
    }

    /**
     * Execute handler
     */
    private function executeHandler($handler, $params)
    {
        if (is_string($handler)) {
            // Format: "Controller@method"
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                $controllerClass = $controller . 'Controller';
                
                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $method)) {
                        return call_user_func_array([$controllerInstance, $method], $params);
                    }
                }
            } else {
                // Just controller name, call index method
                $controllerClass = $handler . 'Controller';
                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, 'index')) {
                        return call_user_func_array([$controllerInstance, 'index'], $params);
                    }
                }
            }
        } elseif (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        throw new Exception("Handler not found: " . print_r($handler, true));
    }

    /**
     * Normalize URI
     */
    private function normalizeUri($uri)
    {
        // Remove query string
        $uri = strtok($uri, '?');
        
        // Remove trailing slash
        $uri = rtrim($uri, '/');
        
        // Add leading slash if not present
        if (empty($uri)) {
            $uri = '/';
        }

        return $uri;
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound()
    {
        http_response_code(404);
        
        // Try to render 404 view
        if (file_exists(__DIR__ . '/../views/errors/404.php')) {
            include __DIR__ . '/../views/errors/404.php';
        } else {
            echo "404 - Page Not Found";
        }
        exit();
    }
}
