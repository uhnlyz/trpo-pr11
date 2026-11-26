<?php

namespace App\Controllers;
declare(strict_types=1);

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
