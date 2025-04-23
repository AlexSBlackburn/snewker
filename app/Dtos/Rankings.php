<?php

declare(strict_types=1);

namespace App\Dtos;

use Illuminate\Support\Collection;

final class Rankings
{
    public function __construct(
        public string $title,
        public Collection $players,
    ) {}
}
