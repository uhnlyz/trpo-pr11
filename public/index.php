<?php
declare(strict_types=1);

use App\Controllers\CartController;
use App\Controllers\CatalogController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Core\Router;
use App\Repositories\JsonCategoryRepository;
use App\Repositories\JsonOrderRepository;
use App\Repositories\JsonProductRepository;
use App\Services\CartService;
use App\Services\OrderValidator;


session_start();

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $segments = explode('\\', $relative);
    $directoryMap = [
        'Controllers' => 'Controller',
        'Models' => 'Model',
        'Repositories' => 'Repository',
        'Services' => 'Service',
    ];
    $segments[0] = $directoryMap[$segments[0]] ?? $segments[0];
    $file = __DIR__ . '/../src/' . implode('/', $segments) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

$root = dirname(__DIR__);
$csrfToken = $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
$blocked = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
    && !hash_equals($csrfToken, (string) ($_POST['csrf_token'] ?? ''));

try {
    if ($blocked) {
        http_response_code(403);
        $title = 'Запрос отклонён';
        $cartCount = 0;
        $content = '<section class="hero"><h1>Запрос отклонён</h1><p>Обновите страницу и повторите действие.</p></section>';
    } else {
        $products = new JsonProductRepository($root . '/dataset/products.json');
        $cartService = new CartService($products);
        $router = new Router($root . '/dataset/routes.csv');
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $route = $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);

        if ($route === null) {
            http_response_code(404);
            $title = 'Страница не найдена';
            ob_start();
            require $root . '/views/not-found.php';
            $content = ob_get_clean();
        } else {
            [$name, $action] = $route;
            if ($name === 'HomeController') {
                $pageData = json_decode((string) file_get_contents($root . '/dataset/pages.json'), true, 512, JSON_THROW_ON_ERROR);
                $controller = new HomeController($pageData);
                $data = $controller->$action();
                $viewFile = $root . '/views/home/index.php';
            } elseif ($name === 'CatalogController') {
                $controller = new CatalogController($products, new JsonCategoryRepository($root . '/dataset/categories.json'));
                $data = $controller->$action();
                if (!empty($data['notFound'])) {
                    http_response_code(404);
                    $viewFile = $root . '/views/not-found.php';
                } else {
                    $viewFile = $action === 'show' ? $root . '/views/catalog/product.php' : $root . '/views/catalog/index.php';
                }
            } elseif ($name === 'CartController') {
                $controller = new CartController($cartService);
                $data = $controller->$action();
                $viewFile = $root . '/views/cart/index.php';
            } elseif ($name === 'CheckoutController') {
                $methods = json_decode((string) file_get_contents($root . '/dataset/delivery_methods.json'), true, 512, JSON_THROW_ON_ERROR);
                $ordersFile = $root . '/data/orders.json';
                if (!is_file($ordersFile)) {
                    if (!is_dir(dirname($ordersFile))) {
                        mkdir(dirname($ordersFile), 0775, true);
                    }
                    copy($root . '/dataset/orders_seed.json', $ordersFile);
                }
                $controller = new CheckoutController($cartService, new OrderValidator(), new JsonOrderRepository($ordersFile), $methods);
                $data = $controller->$action();
                $viewFile = $action === 'success' ? $root . '/views/checkout/success.php' : $root . '/views/checkout/form.php';
            } else {
                throw new RuntimeException('Неизвестный контроллер.');
            }

            extract($data, EXTR_SKIP);
            $title = $title ?? ($data['shop_name'] ?? 'Настольная');
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
        }
        $cartCount = $cartService->count();
    }
} catch (Throwable $exception) {
    http_response_code(500);
    $title = 'Ошибка приложения';
    $cartCount = 0;
    $content = '<section class="hero"><h1>Не удалось открыть страницу</h1><p>Проверьте файлы проекта и dataset.</p></section>';
}

require $root . '/views/layout.php';
