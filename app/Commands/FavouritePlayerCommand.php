<?php

declare(strict_types=1);

namespace App\Commands;

use App\Dtos\Player;
use App\Services\FavouritePlayerService;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\multiselect;

final class FavouritePlayerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'favourite-players';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Favourite players to be notified when they play';

    /**
     * Execute the console command.
     */
    public function handle(FavouritePlayerService $favouritePlayerService): void
    {
        $players = $favouritePlayerService->getAllPlayers()->mapWithKeys(function (Player $player) {
            return [$player->id => $player->name];
        });
        $playerIds = multiselect(
            label: 'Select the players you want to favourite',
            options: $players->toArray(),
            default: DB::table('favourite_players')->pluck('player_id')->toArray(),
        );

        $favouritePlayerService->favouritePlayers(collect($playerIds));

        $this->info('Players favourited successfully');
    }
}
