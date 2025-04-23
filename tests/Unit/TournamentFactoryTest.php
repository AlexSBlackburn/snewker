<?php

declare(strict_types=1);

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
    expect(get_class($this->tournament))->toBe(Tournament::class)
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

        expect($matches)->toHaveCount(2);

        expect($matches['Round 1'])->toHaveCount(11)
            ->and($matches['Round 1']->filter(fn ($match) => $match->status === MatchStatus::COMPLETED)->count())->toBe(5)
            ->and($matches['Round 1']->filter(fn ($match) => $match->status === MatchStatus::LIVE)->count())->toBe(2)
            ->and($matches['Round 1']->filter(fn ($match) => $match->status === MatchStatus::SUSPENDED)->count())->toBe(2)
            ->and($matches['Round 1']->filter(fn ($match) => $match->status === MatchStatus::SCHEDULED)->count())->toBe(2)
        ;

        expect($matches['Round 2'])->toHaveCount(3)
            ->and($matches['Round 2']->filter(fn ($match) => $match->status === MatchStatus::COMPLETED)->count())->toBe(0)
            ->and($matches['Round 2']->filter(fn ($match) => $match->status === MatchStatus::LIVE)->count())->toBe(0)
            ->and($matches['Round 2']->filter(fn ($match) => $match->status === MatchStatus::SUSPENDED)->count())->toBe(0)
            ->and($matches['Round 2']->filter(fn ($match) => $match->status === MatchStatus::SCHEDULED)->count())->toBe(3)
        ;
    });

    it('creates matches correctly', function () {
        $match = $this->tournament->matches['Round 1']->first();

        expect(get_class($match))->toBe(SnookerMatch::class)
            ->and($match->status)->toBe(MatchStatus::COMPLETED)
        ;
    });
});
