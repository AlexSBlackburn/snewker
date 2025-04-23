<?php

declare(strict_types=1);

namespace App\Services;

use App\Dtos\Tournament;
use App\Factories\TournamentFactory;
use Illuminate\Support\Facades\Http;

final readonly class TournamentService
{
    public function __construct(private TournamentFactory $tournamentFactory) {}

    public function getCurrentTournament(): string
    {
        return Http::wst('tournaments')->get('/current')->json('data.id');
    }

    public function getTournament(): Tournament
    {
        $response = Http::wst('tournaments')->get('/'.$this->getCurrentTournament());

        return $this->tournamentFactory->createTournamentFromResponse($response);
    }
}
