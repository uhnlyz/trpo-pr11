<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function __construct(string $routesFile)
    {
        if (!is_file($routesFile)) {
            throw new RuntimeException('Не найден файл routes.csv.');
        }

        $handle = fopen($routesFile, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Не удалось открыть routes.csv.');
        }

        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) {
                continue;
            }

            [$method, $path, $controller, $action] = $row;
            $this->routes[$method][$path] = [$controller, $action];
        }
        fclose($handle);
    }

    public function dispatch(string $method, string $path): ?array
    {
        return $this->routes[$method][$path] ?? null;
    }
}
