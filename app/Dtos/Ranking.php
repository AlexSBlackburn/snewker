<?php

namespace App\Dtos;

final readonly class Ranking
{
    public function __construct(
        public int $position,
        public string $playerName,
    ) {}
}
