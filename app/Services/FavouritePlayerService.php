<?php

declare(strict_types=1);

namespace App\Services;

use App\Factories\PlayerFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

final readonly class FavouritePlayerService
{
    public function __construct(private PlayerFactory $playerFactory) {}

    public function favouritePlayers(Collection $playerIds): void
    {
        DB::table('favourite_players')->truncate();

        if ($playerIds->isNotEmpty()) {
            DB::table('favourite_players')->insert($playerIds->map(fn ($id) => ['player_id' => $id])->toArray());
        }
    }

    public function getAllPlayers(): Collection
    {
        $response = Http::get('https://players.snooker.web.gc.wstservices.co.uk/v2');

        return $this->playerFactory->createPlayersFromResponse($response);
    }
}
