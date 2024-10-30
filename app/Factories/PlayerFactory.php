<?php

namespace App\Factories;

use App\Dtos\Player;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;

final readonly class PlayerFactory
{
    public function createPlayersFromResponse(Response $response): Collection
    {
        return $response->collect('data')
            ->map(function (array $player) {
                return new Player(
                    id: $player['id'],
                    name: $player['attributes']['firstName'].' '.$player['attributes']['surname'],
                );
            })
            ->sortBy('name');
    }
}
