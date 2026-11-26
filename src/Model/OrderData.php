<?php
declare(strict_types=1);

namespace App\Models;
final class OrderData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $address,
        public readonly string $deliveryId,
        public readonly array $items,
        public readonly int $total
    ) {}
}
