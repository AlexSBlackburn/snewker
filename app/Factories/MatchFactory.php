<?php

namespace App\Factories;

use App\Dtos\MatchPlayer;
use App\Dtos\SnookerMatch;
use App\Enums\MatchStatus;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;

final readonly class MatchFactory
{
    public function createMatchFromResponse(Response $response): SnookerMatch
    {
        $match = $response->json('data');
        $attributes = $match['attributes'];

        $playerOne = new MatchPlayer(
            id: $attributes['homePlayer']['playerID'],
            name: $attributes['homePlayer']['firstName'].' '.$attributes['homePlayer']['surname'],
            nationality: $attributes['homePlayer']['country'],
            frames: $attributes['homePlayerScore'],
        );
        $playerTwo = new MatchPlayer(
            id: $attributes['awayPlayer']['playerID'],
            name: $attributes['awayPlayer']['firstName'].' '.$attributes['awayPlayer']['surname'],
            nationality: $attributes['awayPlayer']['country'],
            frames: $attributes['awayPlayerScore'],
        );

        if ($attributes['status'] === 'Live') {
            $currentFrame = $attributes['homePlayerScore'] + $attributes['awayPlayerScore'];

            if (isset($attributes['history']['matchData']['matchHistory']['frames'][$currentFrame])) {
                $playerOne->score = $attributes['history']['matchData']['matchHistory']['frames'][$currentFrame]['homePlayerPoints'];
                $playerTwo->score = $attributes['history']['matchData']['matchHistory']['frames'][$currentFrame]['awayPlayerPoints'];
            }
        }

        return new SnookerMatch(
            id: $match['id'],
            tournament: $attributes['tournament']['name'],
            name: $attributes['name'],
            round: $attributes['round'],
            status: MatchStatus::from($attributes['status']),
            start: new Carbon($attributes['startDateTime']),
            frames: $attributes['numberOfFrames'],
            playerOne: $playerOne,
            playerTwo: $playerTwo,
        );
    }
}
