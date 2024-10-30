<?php

namespace App\Dtos;

use Illuminate\Support\Carbon;

final readonly class SnookerMatch
{
    public function __construct(
        public string $id,
        public string $tournament,
        public string $name,
        public string $round,
        public string $status,
        public Carbon $start,
        public int $frames,
        public MatchPlayer $playerOne,
        public MatchPlayer $playerTwo,
    ) {}
}
