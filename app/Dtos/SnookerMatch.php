<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Enums\MatchStatus;
use Illuminate\Support\Carbon;

final readonly class SnookerMatch
{
    public function __construct(
        public string $id,
        public string $tournament,
        public string $name,
        public string $round,
        public MatchStatus $status,
        public Carbon $start,
        public int $frames,
        public MatchPlayer $playerOne,
        public MatchPlayer $playerTwo,
    ) {}
}
