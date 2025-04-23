<?php

declare(strict_types=1);

namespace App\Services;

use App\Dtos\SnookerMatch;
use App\Factories\MatchFactory;
use Illuminate\Support\Facades\Http;

final readonly class MatchService
{
    public function __construct(private MatchFactory $matchFactory) {}

    public function getMatch(string $id): SnookerMatch
    {
        $response = Http::wst('matches')->get('/'.$id);

        return $this->matchFactory->createMatchFromResponse($response);
    }
}
