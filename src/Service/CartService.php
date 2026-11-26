<?php
declare(strict_types=1);

final class CartService
{
    public function __construct(private JsonProductRepository $products) {}
    public function add(int $productId): bool
    {
        if ($this->products->find($productId) === null) return false;
        $_SESSION['cart'] ??= [];
        $_SESSION['cart'][$productId] = (int)($_SESSION['cart'][$productId] ?? 0) + 1;
        return true;
    }
    public function update(int $productId, int $quantity): bool
    {
        if ($quantity < 1 || $this->products->find($productId) === null || !isset($_SESSION['cart'][$productId])) return false;
        $_SESSION['cart'][$productId] = $quantity;
        return true;
    }
    public function remove(int $productId): void { unset($_SESSION['cart'][$productId]); }
    public function lines(): array
    {
        $lines=[]; foreach(($_SESSION['cart']??[]) as $id=>$quantity) {
            $product=$this->products->find((int)$id);
            if($product!==null && (int)$quantity>0) $lines[]=['product'=>$product,'quantity'=>(int)$quantity,'subtotal'=>$product->price*(int)$quantity];
        }
        return $lines;
    }
    public function total(): int { return array_sum(array_column($this->lines(),'subtotal')); }
    public function count(): int { return array_sum(array_map('intval',$_SESSION['cart']??[])); }
}
