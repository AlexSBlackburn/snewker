<?php

namespace App\Dtos;

final class Ranking
{
    public function __construct(
        public readonly string $playerName,
        public readonly int $position,
        public readonly int $points,
        public int $difference = 0,
    ) {}
}
