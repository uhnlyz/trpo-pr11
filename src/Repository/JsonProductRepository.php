<?php

namespace App\Repositories;

use App\Models\Product;
declare(strict_types=1);
final class JsonProductRepository
{
    public function __construct(private string $file) {}
    public function all(): array
    {
        $rows=json_decode((string)file_get_contents($this->file),true,512,JSON_THROW_ON_ERROR);
        $categories=(new JsonCategoryRepository(dirname($this->file).'/categories.json'))->all();$names=[];
        foreach($categories as $c)$names[$c->id]=$c->name;
        return array_map(static fn(array $r):Product=>new Product((int)$r['id'],(string)$r['name'],(int)$r['price'],(int)$r['category_id'],(string)$r['description'],(int)$r['stock'],$names[(int)$r['category_id']]??'Без категории'),$rows);
    }
    public function find(int $id): ?Product { foreach($this->all() as $p)if($p->id===$id)return $p;return null; }
}
