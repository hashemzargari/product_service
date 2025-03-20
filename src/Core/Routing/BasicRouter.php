<?php

namespace Hertz\ProductService\Core\Routing;

use Hertz\ProductService\Core\Http\Request;

class BasicRouter
{
    private const ROUTES_CACHE_FILE = __DIR__ . '/../../var/cache/routes.php';

    /**
     * @var array<string, Route>
     */
    private array $staticRoutes = [];

    /**
     * @var Route[]
     */
    private array $dynamicRoutes = [];

    /**
     * Radix trie node structure
     */
    private ?array $routeTrie = null;

    public function addRoute(Route $route): void
    {
        if ($route->isStatic()) {
            $key = "{$route->getMethod()} {$route->getPath()}";
            $this->staticRoutes[$key] = $route;
        } else {
            $this->dynamicRoutes[] = $route;
            // Reset trie when adding new dynamic route
            $this->routeTrie = null;
        }
    }

    private function generateRouteKey(Request $request): string
    {
        return "{$request->getMethod()} {$request->getPath()}";
    }

    public function match(Request $request): ?Route
    {
        $key = $this->generateRouteKey($request);

        // First check static routes
        if (isset($this->staticRoutes[$key])) {
            return $this->staticRoutes[$key];
        }

        // If no static match, check dynamic routes
        if (empty($this->dynamicRoutes)) {
            return null;
        }

        // Load or build the route trie
        if ($this->routeTrie === null) {
            $this->loadRoutes();
        }

        // Search the trie
        return $this->searchTrie($key);
    }

    private function loadRoutes(): void
    {
        if (file_exists(self::ROUTES_CACHE_FILE)) {
            $this->routeTrie = require self::ROUTES_CACHE_FILE;
            return;
        }

        $this->buildRouteTrie();

        // Ensure cache directory exists
        $cacheDir = dirname(self::ROUTES_CACHE_FILE);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        // Save compiled routes to cache file
        file_put_contents(
            self::ROUTES_CACHE_FILE,
            '<?php return ' . var_export($this->routeTrie, true) . ';'
        );
    }

    private function buildRouteTrie(): void
    {
        $this->routeTrie = [];

        foreach ($this->dynamicRoutes as $route) {
            $pattern = $route->getMethod() . ' ' . $route->getPath();
            $segments = explode('/', trim($pattern, '/'));

            $current = &$this->routeTrie;
            $paramIndex = 0;
            foreach ($segments as $segment) {
                if (preg_match('/\{([^}]+)\}/', $segment)) {
                    $key = '*';
                    if (!isset($current['params'])) {
                        $current['params'] = [];
                    }
                    $current['params'][$paramIndex++] = trim($segment, '{}');
                } else {
                    $key = $segment;
                }

                if (!isset($current[$key])) {
                    $current[$key] = [];
                }
                $current = &$current[$key];
            }
            $current['route'] = $route;
        }
    }

    private function searchTrie(string $path): ?Route
    {
        $segments = explode('/', trim($path, '/'));
        $params = [];
        $current = $this->routeTrie;
        $paramIndex = 0;

        foreach ($segments as $segment) {
            if (isset($current[$segment])) {
                $current = $current[$segment];
            } elseif (isset($current['*']) && isset($current['params'][$paramIndex])) {
                $params[$current['params'][$paramIndex]] = $segment;
                $current = $current['*'];
                $paramIndex++;
            } else {
                return null;
            }
        }

        if (isset($current['route'])) {
            $route = $current['route'];
            $route->setParams($params);
            return $route;
        }

        return null;
    }
}