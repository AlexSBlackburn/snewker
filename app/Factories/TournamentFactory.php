<?php

namespace App\Factories;

use App\Dtos\MatchPlayer;
use App\Dtos\SnookerMatch;
use App\Dtos\Tournament;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;

final readonly class TournamentFactory
{
    public function createTournamentFromResponse(Response $response): Tournament
    {
        $tournament = $response->json('data.attributes');

        $matches = collect($tournament['matches'])->map(function (array $match) {
            $playerOne = new MatchPlayer(
                id: $match['homePlayer']['playerID'],
                name: $match['homePlayer']['firstName'] . ' ' . $match['homePlayer']['surname'],
                nationality: $match['homePlayer']['country'],
                frames: $match['homePlayerScore'],
            );
            $playerTwo = new MatchPlayer(
                id: $match['awayPlayer']['playerID'],
                name: $match['awayPlayer']['firstName'] . ' ' . $match['awayPlayer']['surname'],
                nationality: $match['awayPlayer']['country'],
                frames: $match['awayPlayerScore'],
            );

            return new SnookerMatch(
                id: $match['matchID'],
                tournament: $match['name'],
                name: $match['name'],
                round: $match['round'],
                status: $match['status'],
                start: Carbon::parse($match['startDateTime']),
                frames: $match['numberOfFrames'],
                playerOne: $playerOne,
                playerTwo: $playerTwo,
            );
        });
        $completedMatches = $matches->filter(fn (SnookerMatch $match) => $match->status === 'Completed')->reverse()->take(5);
        $liveMatches = $matches->filter(fn (SnookerMatch $match) => $match->status === 'Live');
        $scheduledMatches = $matches->filter(fn (SnookerMatch $match) => $match->status === 'Scheduled')->take(5);

        return new Tournament(
            name: $tournament['name'],
            location: $tournament['city'] . ', ' . $tournament['country'],
            matches: $completedMatches->merge($liveMatches)->merge($scheduledMatches)->groupBy('round'),
        );
    }
}
