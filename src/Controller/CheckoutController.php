<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\OrderData;
use App\Repositories\JsonOrderRepository;
use App\Services\CartService;
use App\Services\OrderValidator;

final class CheckoutController
{
    public function __construct(
        private CartService $cart,
        private OrderValidator $validator,
        private JsonOrderRepository $orders,
        private array $deliveryMethods
    ) {
    }

    public function form(): array
    {
        $errors = $_SESSION['checkout_errors'] ?? [];
        $old = $_SESSION['checkout_old'] ?? [];
        unset($_SESSION['checkout_errors'], $_SESSION['checkout_old']);

        $deliveryPrice = 0;
        foreach ($this->deliveryMethods as $method) {
            if (($method['id'] ?? '') === ($old['delivery'] ?? '')) {
                $deliveryPrice = (int) ($method['price'] ?? 0);
                break;
            }
        }

        return [
            'title' => 'Оформление заказа',
            'empty' => $this->cart->lines() === [],
            'deliveryMethods' => $this->deliveryMethods,
            'errors' => $errors,
            'old' => $old,
            'lines' => $this->cart->lines(),
            'total' => $this->cart->total(),
            'deliveryPrice' => $deliveryPrice,
        ];
    }

    public function submit(): void
    {
        $input = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'delivery' => (string) ($_POST['delivery'] ?? ''),
        ];
        $errors = $this->validator->validate($input, $this->deliveryMethods);

        if ($errors !== [] || $this->cart->lines() === []) {
            $_SESSION['checkout_errors'] = $errors;
            $_SESSION['checkout_old'] = $input;
            header('Location: /checkout');
            exit;
        }

        $deliveryPrice = 0;
        $deliveryName = '';
        foreach ($this->deliveryMethods as $method) {
            if (($method['id'] ?? '') === $input['delivery']) {
                $deliveryPrice = (int) ($method['price'] ?? 0);
                $deliveryName = (string) ($method['name'] ?? '');
                break;
            }
        }

        $items = array_map(
            static fn (array $line): array => [
                'product_id' => $line['product']->id,
                'name' => $line['product']->name,
                'price' => $line['product']->price,
                'quantity' => $line['quantity'],
            ],
            $this->cart->lines()
        );

        $order = new OrderData(
            $input['name'],
            $input['email'],
            $input['address'],
            $input['delivery'],
            $items,
            $this->cart->total() + $deliveryPrice
        );

        $id = $this->orders->create($order);
        unset($_SESSION['cart'], $_SESSION['checkout_errors'], $_SESSION['checkout_old']);
        header('Location: /order-success?id=' . $id);
        exit;
    }

    public function success(): array
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $order = $id ? $this->orders->find((int) $id) : null;
        if ($order === null) {
            http_response_code(404);
        }

        $deliveryName = '';
        if ($order !== null) {
            foreach ($this->deliveryMethods as $method) {
                if (($method['id'] ?? '') === ($order['delivery_id'] ?? '')) {
                    $deliveryName = (string) ($method['name'] ?? '');
                    break;
                }
            }
        }

        return ['title' => 'Заказ оформлен', 'order' => $order, 'deliveryName' => $deliveryName];
    }
}
