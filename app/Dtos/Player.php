<?php

declare(strict_types=1);

namespace App\Dtos;

final class Player
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
