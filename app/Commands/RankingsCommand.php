<?php

declare(strict_types=1);

namespace App\Commands;

use App\Dtos\Ranking;
use App\Services\RankingsService;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Helper\TableSeparator;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

final class RankingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show world rankings';

    /**
     * Execute the console command.
     */
    public function handle(RankingsService $rankingsService): void
    {
        $rankings = spin(message: 'Fetching rankings...', callback: fn () => $rankingsService->getRankings());

        $this->info($rankings->title);
        $this->newLine();

        $topPlayers = $rankings->players->take(16);
        $tourCardPlayers = $rankings->players->slice(16, 64 - 16);
        $remainingPlayers = $rankings->players->slice(64);

        table(
            headers: ['Rank', 'Player', 'Points', 'Difference'],
            rows: $topPlayers->map(fn (Ranking $ranking) => [$ranking->position, $ranking->playerName, $ranking->points, $ranking->difference])
                ->merge([new TableSeparator])
                ->merge($tourCardPlayers->map(fn (Ranking $ranking) => [$ranking->position, $ranking->playerName, $ranking->points, $ranking->difference]))
                ->merge([new TableSeparator])
                ->merge($remainingPlayers->map(fn (Ranking $ranking) => [$ranking->position, $ranking->playerName, $ranking->points, $ranking->difference]))
                ->toArray()
        );
    }
}
