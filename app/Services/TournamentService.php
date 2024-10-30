<?php

namespace App\Services;

use App\Factories\TournamentFactory;
use Illuminate\Support\Facades\Http;

final readonly class TournamentService
{
    public function __construct(private TournamentFactory $tournamentFactory)
    {
    }

    public function getCurrentTournament()
    {
        return Http::get('https://tournaments.snooker.web.gc.wstservices.co.uk/v2/current')->json('data.id');
    }

    public function getTournament()
    {
        $response = Http::get('https://tournaments.snooker.web.gc.wstservices.co.uk/v2/' . $this->getCurrentTournament());

        return $this->tournamentFactory->createTournamentFromResponse($response);
    }
}
