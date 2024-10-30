<?php

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
            ->map(function (array $ranking) {
                return new Ranking(
                    position: $ranking['position'],
                    playerName: $ranking['player']['firstName'] . ' ' . $ranking['player']['surname'],
                );
            })
            ->sortBy('position');

        return new Rankings(
            title: $response->json('data.0.attributes.recalculateAfter'),
            players: $players,
        );
    }
}
