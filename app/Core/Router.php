<?php
/**
 * Core HTTP Router (Mendukung GET & POST serta Subfolder Deployment)
 */

declare(strict_types=1);

namespace App\Core;

class Router
{
    /** @var array<string, array<string, array{0: class-string, 1: string}>> */
    private array $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$this->normalizePath($path)] = $handler;
    }

    /**
     * Mendeteksi base URL aplikasi (mendukung root "/" maupun subfolder "/PayCom_V2").
     */
    public static function basePath(): string
    {
        $docRoot = rtrim(str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        $appRoot = rtrim(str_replace('\\', '/', (string) realpath(dirname(__DIR__, 2))), '/');

        if ($docRoot !== '' && $appRoot !== '') {
            if (strcasecmp($appRoot, $docRoot) === 0) {
                return '';
            }
            $prefix = $docRoot . '/';
            $matches = DIRECTORY_SEPARATOR === '\\'
                ? stripos($appRoot, $prefix) === 0
                : str_starts_with($appRoot, $prefix);

            if ($matches) {
                return substr($appRoot, strlen($docRoot));
            }
        }

        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $dir = str_replace('\\', '/', dirname($scriptName));
        $dir = rtrim($dir, '/');
        if ($dir === '' || $dir === '.') {
            return '';
        }
        return $dir;
    }

    /**
     * Menyelesaikan path rute aktual dari request URI atau parameter ?route=...
     */
    public function resolveRequestPath(): string
    {
        if (!empty($_GET['route']) && is_string($_GET['route'])) {
            return $this->normalizePath($_GET['route']);
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        $base = self::basePath();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        if (str_starts_with($path, '/index.php')) {
            $path = substr($path, strlen('/index.php'));
        }

        return $this->normalizePath($path);
    }

    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = $this->resolveRequestPath();

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            $title = '404 - Halaman Tidak Ditemukan';
            $requestedPath = $path;
            require dirname(__DIR__) . '/Views/errors/404.php';
            return;
        }

        [$controllerClass, $action] = $this->routes[$method][$path];
        $controller = new $controllerClass();
        $controller->$action();
    }

    /**
     * @return array<string, array<string, array{0: class-string, 1: string}>>
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    private function normalizePath(string $path): string
    {
        $trimmed = '/' . trim($path, '/');
        return $trimmed === '' ? '/' : $trimmed;
    }
}

