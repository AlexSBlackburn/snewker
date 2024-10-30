<?php

namespace App\Commands;

use App\Dtos\SnookerMatch;
use App\Services\MatchService;
use App\Services\NotificationService;
use App\Services\TournamentService;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class MatchesCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'matches';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display current tournament matches';

    /**
     * Execute the console command.
     */
    public function handle(TournamentService $tournamentService, MatchService $matchService, NotificationService $notificationService): void
    {
        clear();
        $this->newLine(2);

        $tournament = spin(message: 'Fetching matches...', callback: fn () => $tournamentService->getTournament());

        $this->info($tournament->name);
        $this->newLine();

        $tournament->matches->each(function (Collection $matches, string $round) use ($matchService, $notificationService) {
            $this->info($round);
            $this->newLine();

            table(
                headers: ['Start', 'Player One', 'Points', 'Frames', 'Points', 'Player Two'],
                rows: $matches->map(function (SnookerMatch $match) use ($matchService, $notificationService) {
                    $status = $match->status === 'Scheduled' ? $match->start->diffForHumans() : $match->status;

                    if ($status === 'Live') {
                        // Get points for live matches
                        $match = $matchService->getMatch($match->id);
                    }

                    return [
                        $status,
                        $match->playerOne->name . ' (' . $match->playerOne->nationality . ')',
                        $match->playerOne->score,
                        $match->playerOne->frames . ' (' . $match->frames . ') ' . $match->playerTwo->frames,
                        $match->playerTwo->score,
                        $match->playerTwo->name . ' (' . $match->playerTwo->nationality . ')',
                    ];
                })->toArray()
            );
        });

        $tournament->matches->each(function (Collection $matches) use ($notificationService) {
            $matches->each(function (SnookerMatch $match) use ($notificationService) {
                if ($notificationService->shouldNotifyAboutFavouritePlayer($match->playerOne, $match)) {
                    $message = $notificationService->getFavouritePlayerNotification($match->playerOne, $match);
                    $this->notify('Snewker', $message);
                }
                if ($notificationService->shouldNotifyAboutFavouritePlayer($match->playerTwo, $match)) {
                    $message = $notificationService->getFavouritePlayerNotification($match->playerTwo, $match);
                    $this->notify('Snewker', $message);
                }
            });
        });

        $this->newLine();
        $this->info('Last fetched: ' . now()->timezone('Europe/Amsterdam')->format('H:i:s'));
    }
}
