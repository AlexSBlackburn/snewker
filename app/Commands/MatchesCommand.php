<?php

namespace App\Commands;

use App\Dtos\SnookerMatch;
use App\Enums\MatchStatus;
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
    public function handle(TournamentService $tournamentService, MatchService $matchService, NotificationService $notificationService): int
    {
        while (true) {
            clear();
            $this->newLine(2);

            try {
                $tournament = spin(message: 'Fetching matches...', callback: fn() => $tournamentService->getTournament());
            } catch (\Exception $e) {
                $this->info($e->getMessage());
                return Command::FAILURE;
            }

            $this->info($tournament->name);
            $this->newLine();
            $this->info($tournament->startDate->format('D d M Y') . ' - ' . $tournament->endDate->format('D d M Y'));
            $this->newLine();
            $this->info($tournament->location);
            $this->newLine();

            $tournament->matches->each(function (Collection $matches, string $round) use ($matchService) {
                $this->info($round);
                $this->newLine();

                table(
                    headers: ['Start', 'Player One', 'Points', 'Frames', 'Points', 'Player Two'],
                    rows: $matches->map(function (SnookerMatch $match) use ($matchService) {
                        $status = in_array($match->status, [MatchStatus::SCHEDULED, MatchStatus::SUSPENDED]) ? $match->start->diffForHumans() : $match->status->value;

                        if ($status === MatchStatus::LIVE->value) {
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
                        $this->notify('Snewker', $notificationService->getFavouritePlayerNotification($match->playerOne, $match));
                    }
                    if ($notificationService->shouldNotifyAboutFavouritePlayer($match->playerTwo, $match)) {
                        $this->notify('Snewker', $notificationService->getFavouritePlayerNotification($match->playerTwo, $match));
                    }
                });
            });

            $this->newLine();
            $this->info('Last fetched: ' . now()->timezone('Europe/Amsterdam')->format('H:i:s'));
            sleep(60);
        }
    }
}
