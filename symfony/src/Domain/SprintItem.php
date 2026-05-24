<?php

namespace App\Domain;

final readonly class SprintItem
{
    public function __construct(
        public int $id,
        public string $title,
        public string $category,
        public string $status,
        public int $weight,
    ) {
    }
}
