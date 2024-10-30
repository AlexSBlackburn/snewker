<?php

namespace App\Dtos;

use Illuminate\Support\Collection;

final readonly class Tournament
{
    public function __construct(
        public string $name,
        public Collection $matches,
    )
    {
    }
}
