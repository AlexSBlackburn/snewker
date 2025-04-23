<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

test('matches command is successful', function () {
    Http::fake([
        '*/current' => Http::response(file_get_contents(base_path('tests/stubs/current-tournament.json'))),
        '*/de7dda21-f1be-4073-a726-72e2167a0bea' => Http::response(file_get_contents(base_path('tests/stubs/tournament.json'))),
    ]);
//    Sleep::fake();

    $this->artisan('matches')->assertExitCode(0);
})->only();
