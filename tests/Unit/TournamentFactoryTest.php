<?php

declare(strict_types=1);

use App\Dtos\MatchPlayer;
use App\Dtos\SnookerMatch;
use App\Dtos\Tournament;
use App\Enums\MatchStatus;
use App\Factories\TournamentFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $factory = app(TournamentFactory::class);
    $response = new Response(new \GuzzleHttp\Psr7\Response(200, [], file_get_contents(__DIR__.'/../stubs/tournament.json')));

    $this->tournament = $factory->createTournamentFromResponse($response);
});

it('creates tournament from response', function () {
    expect($this->tournament)->toBeInstanceOf(Tournament::class)
        ->and($this->tournament->name)->toBe('Halo World Championship 2025')
        ->and($this->tournament->location)->toBe('Sheffield, England')
        ->and($this->tournament->startDate)->toBeInstanceOf(Carbon::class)
        ->and($this->tournament->startDate->toDateString())->toBe('2025-04-19')
        ->and($this->tournament->endDate)->toBeInstanceOf(Carbon::class)
        ->and($this->tournament->endDate->toDateString())->toBe('2025-05-05')
    ;
});

describe('matches from tournament response', function () {
    it('contains correct match counts', function () {
        $matches = $this->tournament->matches;

        expect($matches)->toHaveCount(2)
            ->and($matches)->toHaveKeys(['Round 1', 'Round 2']);

        expect($matches['Round 1'])->toHaveCount(11)
            ->and($matches['Round 1'])->toContainOnlyInstancesOf(SnookerMatch::class)
            ->and($matches['Round 1']->filter(fn ($match) => $match->status === MatchStatus::COMPLETED)->count())->toBe(5)
            ->and($matches['Round 1']->filter(fn ($match) => $match->status === MatchStatus::LIVE)->count())->toBe(2)
            ->and($matches['Round 1']->filter(fn ($match) => $match->status === MatchStatus::SUSPENDED)->count())->toBe(2)
            ->and($matches['Round 1']->filter(fn ($match) => $match->status === MatchStatus::SCHEDULED)->count())->toBe(2)
        ;

        expect($matches['Round 2'])->toHaveCount(3)
            ->and($matches['Round 2'])->toContainOnlyInstancesOf(SnookerMatch::class)
            ->and($matches['Round 2']->filter(fn ($match) => $match->status === MatchStatus::COMPLETED)->count())->toBe(0)
            ->and($matches['Round 2']->filter(fn ($match) => $match->status === MatchStatus::LIVE)->count())->toBe(0)
            ->and($matches['Round 2']->filter(fn ($match) => $match->status === MatchStatus::SUSPENDED)->count())->toBe(0)
            ->and($matches['Round 2']->filter(fn ($match) => $match->status === MatchStatus::SCHEDULED)->count())->toBe(3)
        ;
    });

    it('creates matches correctly', function () {
        $match = $this->tournament->matches['Round 1']->first();

        expect($match)->toBeInstanceOf(SnookerMatch::class)
            ->and($match->id)->toBe('5ef94272-0b7d-4d0d-ae05-a48016c487a0')
            ->and($match->tournament)->toBe('Halo World Championship 2025')
            ->and($match->name)->toBe('Jak Jones vs Zhao Xintong')
            ->and($match->round)->toBe('Round 1')
            ->and($match->status)->toBe(MatchStatus::COMPLETED)
            ->and($match->frames)->toBe(19)
            ->and($match->start)->toBeInstanceOf(Carbon::class)
            ->and($match->start->toDateTimeString())->toBe('2025-04-21 09:00:00')
            ->and($match->playerOne)->toBeInstanceOf(MatchPlayer::class)
            ->and($match->playerTwo)->toBeInstanceOf(MatchPlayer::class)
        ;
    });
});

describe('players from tournament response', function () {
    it('creates player one correctly', function () {
        $match = $this->tournament->matches['Round 1']->first();
        $player = $match->playerOne;

        expect($player)->toBeInstanceOf(MatchPlayer::class)
            ->and($player->id)->toBe('036bc430-6c51-4d63-a366-a6ca218f7f39')
            ->and($player->name)->toBe('Jak Jones')
            ->and($player->nationality)->toBe('WAL')
            ->and($player->frames)->toBe(4)
        ;
    });

    it('creates player two correctly', function () {
        $match = $this->tournament->matches['Round 1']->first();
        $player = $match->playerTwo;

        expect($player)->toBeInstanceOf(MatchPlayer::class)
            ->and($player->id)->toBe('895d376f-9f42-4e67-8a63-bc78676d0726')
            ->and($player->name)->toBe('Zhao Xintong')
            ->and($player->nationality)->toBe('CHN')
            ->and($player->frames)->toBe(10)
        ;
    });
});
