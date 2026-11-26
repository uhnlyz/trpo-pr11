<?php

namespace App\Models;
declare(strict_types=1);
final class Product
{
    public function __construct(public readonly int $id, public readonly string $name, public readonly int $price, public readonly int $categoryId, public readonly string $description, public readonly int $stock, public readonly string $categoryName = '') {}
    public function formattedPrice(): string { return number_format($this->price, 0, ',', ' ') . ' ₽'; }
}
