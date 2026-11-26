<?php
declare(strict_types=1);
final class Category
{
    public function __construct(public readonly int $id, public readonly string $name) {}
}
