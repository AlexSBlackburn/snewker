<?php

namespace App\Dtos;

use Illuminate\Support\Collection;

class Rankings
{
    public function __construct(
        public string $title,
        public Collection $players,
    ) {}
}
