<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
final class JsonCategoryRepository
{
    public function __construct(private string $file) {}
    public function all(): array
    {
        $rows=json_decode((string)file_get_contents($this->file),true,512,JSON_THROW_ON_ERROR);
        return array_map(static fn(array $r):Category=>new Category((int)$r['id'],(string)$r['name']),$rows);
    }
    public function find(int $id): ?Category { foreach($this->all() as $c) if($c->id===$id)return $c; return null; }
}
