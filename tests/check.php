<?php
declare(strict_types=1);

use App\Repositories\JsonProductRepository;
use App\Services\CartService;
use App\Services\OrderValidator;

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = substr($class, strlen($prefix));
    $file = dirname(__DIR__) . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});

function check(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, "ОШИБКА: {$message}\n");
        exit(1);
    }
}

$root = dirname(__DIR__);
$validator = new OrderValidator();
$valid = ['name'=>'Иван','email'=>'ivan@example.com','address'=>'Москва','delivery'=>'courier'];
$methods = json_decode((string) file_get_contents($root.'/dataset/delivery_methods.json'), true, 512, JSON_THROW_ON_ERROR);
check($validator->validate($valid, $methods) === [], 'Корректные данные заказа должны пройти проверку.');
$invalid = $valid;
$invalid['email'] = 'wrong';
check(isset($validator->validate($invalid, $methods)['email']), 'Некорректный email должен быть отклонён.');
$invalid = $valid;
$invalid['delivery'] = 'unknown';
check(isset($validator->validate($invalid, $methods)['delivery']), 'Неизвестная доставка должна быть отклонена.');

$products = new JsonProductRepository($root.'/dataset/products.json');
check($products->find(1) !== null, 'Товар с id=1 должен существовать.');
check($products->find(999) === null, 'Неизвестный товар должен возвращать null.');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$cart = new CartService($products);
check($cart->add(1), 'Существующий товар должен добавляться в корзину.');
check(!$cart->update(1, 0), 'Нулевое количество должно отклоняться.');
check(!$cart->update(1, -2), 'Отрицательное количество должно отклоняться.');
check($cart->update(1, 2), 'Положительное количество должно сохраняться.');
check($cart->total() === 4980, 'Итоговая сумма двух товаров должна быть 4980.');
$cart->remove(1);
check($cart->count() === 0, 'После удаления корзина должна быть пустой.');

echo "Проверки пройдены.\n";
