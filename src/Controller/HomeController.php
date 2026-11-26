<?php
declare(strict_types=1);

namespace App\Controllers;

final class HomeController
{
    public function __construct(private array $pageData)
    {
    }

    public function index(): array
    {
        return $this->pageData;
    }
}
