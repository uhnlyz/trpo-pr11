<?php

namespace App\Controllers;

use App\Models\Product;
use App\Repositories\JsonProductRepository;
use App\Repositories\JsonCategoryRepository;
declare(strict_types=1);
final class CatalogController
{
    public function __construct(private JsonProductRepository $products, private JsonCategoryRepository $categories) {}
    public function index(): array
    {
        $categoryId = isset($_GET['category']) && ctype_digit((string) $_GET['category']) ? (int) $_GET['category'] : null;
        $sort = (string) ($_GET['sort'] ?? 'name');
        $products = $this->products->all();
        if ($categoryId !== null) $products = array_values(array_filter($products, static fn(Product $p): bool => $p->categoryId === $categoryId));
        if ($sort === 'price_asc') usort($products, static fn(Product $a, Product $b): int => $a->price <=> $b->price);
        else usort($products, static fn(Product $a, Product $b): int => strcmp($a->name, $b->name));
        return ['title'=>'Каталог настольных игр','products'=>$products,'categories'=>$this->categories->all(),'selectedCategory'=>$categoryId,'sort'=>$sort];
    }
    public function show(): array
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $product = $id ? $this->products->find((int)$id) : null;
        if ($product === null) { http_response_code(404); return ['notFound'=>true,'title'=>'Товар не найден']; }
        return ['product'=>$product,'title'=>$product->name,'notFound'=>false];
    }
}
