<?php

declare(strict_types=1);

namespace App\Dtos;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final readonly class Tournament
{
    public function __construct(
        public string $name,
        public Carbon $startDate,
        public Carbon $endDate,
        public string $location,
        public Collection $matches,
    ) {}
}
