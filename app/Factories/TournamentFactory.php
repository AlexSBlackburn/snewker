<?php

namespace App\Factories;

use App\Dtos\MatchPlayer;
use App\Dtos\SnookerMatch;
use App\Dtos\Tournament;
use App\Enums\MatchStatus;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;

final readonly class TournamentFactory
{
    public function createTournamentFromResponse(Response $response): Tournament
    {
        $tournament = $response->json('data.attributes');

        $matches = collect($tournament['matches'])->map(function (array $match) {
            if (!$match['playersAllocated']) {
                return false;
            }

            $playerOne = new MatchPlayer(
                id: $match['homePlayer']['playerID'],
                name: $match['homePlayer']['firstName'].' '.$match['homePlayer']['surname'],
                nationality: $match['homePlayer']['country'],
                frames: $match['homePlayerScore'],
            );
            $playerTwo = new MatchPlayer(
                id: $match['awayPlayer']['playerID'],
                name: $match['awayPlayer']['firstName'].' '.$match['awayPlayer']['surname'],
                nationality: $match['awayPlayer']['country'],
                frames: $match['awayPlayerScore'],
            );

            return new SnookerMatch(
                id: $match['matchID'],
                tournament: $match['name'],
                name: $match['name'],
                round: $match['round'],
                status: MatchStatus::from($match['status']),
                start: Carbon::parse($match['startDateTime']),
                frames: $match['numberOfFrames'],
                playerOne: $playerOne,
                playerTwo: $playerTwo,
            );
        })->filter();

        $completedMatches = $matches->filter(fn (SnookerMatch $match) => $match->status === MatchStatus::COMPLETED)->reverse()->take(5);
        $liveMatches = $matches->filter(fn (SnookerMatch $match) => $match->status === MatchStatus::LIVE);
        $suspendedMatches = $matches->filter(fn (SnookerMatch $match) => $match->status === MatchStatus::SUSPENDED);
        $scheduledMatches = $matches->filter(fn (SnookerMatch $match) => $match->status === MatchStatus::SCHEDULED)->take(5);

        $matches = $completedMatches->merge($liveMatches)->merge($suspendedMatches)->merge($scheduledMatches)->groupBy('round');
        $finalMatches = $matches['Final'] ?? collect();
        $semiFinalMatches = $matches['Semi-final'] ?? collect();
        $quarterFinalMatches = $matches['Quarter-final'] ?? collect();
        unset($matches['Final']);
        unset($matches['Semi-final']);
        unset($matches['Quarter-final']);
        $matches = $matches->merge($quarterFinalMatches)->merge($semiFinalMatches)->merge($finalMatches);

        return new Tournament(
            name: $tournament['name'],
            startDate: Carbon::parse($tournament['startDate']),
            endDate: Carbon::parse($tournament['endDate']),
            location: $tournament['city'].', '.$tournament['country'],
            matches: $matches,
        );
    }
}
