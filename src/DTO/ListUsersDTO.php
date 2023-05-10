<?php

declare(strict_types=1);

namespace App\DTO;

final class ListUsersDTO
{
    private ?string $search;

    public function __construct(?string $query)
    {

        $this->search = $query;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }
}