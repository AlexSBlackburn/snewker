<?php

declare(strict_types=1);

namespace App\Services;

use App\Dtos\MatchPlayer;
use App\Dtos\SnookerMatch;
use Illuminate\Support\Facades\DB;

final readonly class NotificationService
{
    public function shouldNotifyAboutFavouritePlayer(MatchPlayer $player, SnookerMatch $match): bool
    {
        if (! in_array($match->status, ['Live', 'Completed'])) {
            return false;
        }

        if (DB::table('favourite_players')->where('player_id', $player->id)->doesntExist()) {
            return false;
        }

        return DB::table('notifications')
            ->where('match_id', $match->id)
            ->where('player_id', $player->id)
            ->where('status', $match->status)
            ->doesntExist();
    }

    public function getFavouritePlayerNotification(MatchPlayer $player, SnookerMatch $match): string
    {
        DB::table('notifications')->insert([
            'match_id' => $match->id,
            'player_id' => $player->id,
            'status' => $match->status,
        ]);

        return match ($match->status) {
            'Live' => sprintf('Your favourite player %s is playing!', $player->name),
            'Completed' => sprintf(
                'Your favourite player %s %s against %s, %d - %d!',
                $player->name,
                $player->frames > $match->playerTwo->frames ? 'won' : 'lost',
                $match->playerTwo->name,
                $player->frames,
                $match->playerTwo->frames
            ),
        };
    }
}
