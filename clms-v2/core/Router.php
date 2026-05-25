<?php
/**
 * CLMS 2.0 — Router.php
 * Simple front-controller router: maps URL paths to module files.
 * .htaccess rewrites all requests to index.php.
 */

class Router
{
    private static array $routes = [];

    public static function get(string $pattern, string $handler): void
    {
        self::$routes[] = ['GET', $pattern, $handler];
    }

    public static function post(string $pattern, string $handler): void
    {
        self::$routes[] = ['POST', $pattern, $handler];
    }

    public static function any(string $pattern, string $handler): void
    {
        self::$routes[] = ['ANY', $pattern, $handler];
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        // Normalise path: strip query string and APP_BASE prefix
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = APP_BASE;
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $uri = rtrim($uri, '/') ?: '/';

        foreach (self::$routes as [$verb, $pattern, $handler]) {
            if ($verb !== 'ANY' && $verb !== $method) continue;

            // Convert /users/{id:\d+} → regex
            $regex = self::patternToRegex($pattern);
            if (preg_match($regex, $uri, $matches)) {
                // Named captures become $_GET keys
                foreach ($matches as $key => $val) {
                    if (!is_int($key)) $_GET[$key] = $val;
                }
                self::runHandler($handler);
                return;
            }
        }

        // 404
        http_response_code(404);
        include CLMS_ROOT . '/modules/errors/404.php';
    }

    private static function patternToRegex(string $pattern): string
    {
        // Replace {name} and {name:\d+}
        $regex = preg_replace_callback('/\{(\w+)(?::([^}]+))?\}/', function ($m) {
            $name  = $m[1];
            $inner = $m[2] ?? '[^/]+';
            return "(?P<$name>$inner)";
        }, $pattern);
        return '#^' . $regex . '$#';
    }

    private static function runHandler(string $handler): void
    {
        // Handler is a file path relative to CLMS_ROOT
        $file = CLMS_ROOT . '/' . ltrim($handler, '/');
        if (!file_exists($file)) {
            error_log("CLMS Router: handler file not found: $file");
            http_response_code(500);
            echo 'Internal error.';
            return;
        }
        include $file;
    }
}
