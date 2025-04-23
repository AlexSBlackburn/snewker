<?php

declare(strict_types=1);

namespace App\Factories;

use App\Dtos\Ranking;
use App\Dtos\Rankings;
use Illuminate\Http\Client\Response;

final readonly class RankingsFactory
{
    public function createRankingsFromResponse(Response $response): Rankings
    {
        $players = $response
            ->collect('data.0.attributes.positions')
            ->transform(function (array $ranking) {
                return new Ranking(
                    playerName: $ranking['player']['firstName'].' '.$ranking['player']['surname'],
                    position: $ranking['position'],
                    points: $ranking['prizeMoney'],
                );
            })
            ->sortBy('position');

        $players->transform(function (Ranking $ranking, int $key) use ($players) {
            $previous = $players->get($key - 1);
            $ranking->difference = $previous ? $ranking->points - $previous->points : 0;

            return $ranking;
        });

        return new Rankings(
            title: $response->json('data.0.attributes.recalculateAfter'),
            players: $players,
        );
    }
}
