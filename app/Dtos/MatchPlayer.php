<?php

declare(strict_types=1);

namespace App\Dtos;

final class MatchPlayer
{
    public function __construct(
        public string $id,
        public string $name,
        public string $nationality,
        public int $score = 0,
        public int $frames = 0,
    ) {}
}
