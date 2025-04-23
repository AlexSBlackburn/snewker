<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;

test('matches command is successful', function () {
    $this->freezeTime(function () {
        Http::fake([
            '*/current' => Http::response(file_get_contents(base_path('tests/stubs/current-tournament.json'))),
            '*/de7dda21-f1be-4073-a726-72e2167a0bea' => Http::response(file_get_contents(base_path('tests/stubs/tournament.json'))),
            '*matches*' => Http::response(file_get_contents(base_path('tests/stubs/match.json'))),
        ]);

        $this->artisan('matches')->assertExitCode(0)
            ->expectsOutputToContain('Halo World Championship 2025')
            ->expectsOutputToContain('Sat 19 Apr 2025 - Mon 05 May 2025')
            ->expectsOutputToContain('Sheffield, England')
            ->expectsOutputToContain('Round 1')
//            ->expectsTable(
//                [
//                    'Start',
//                    'Player One',
//                    'Points',
//                    'Frames',
//                    'Points',
//                    'Player Two',
//                ],
//                [
//                    ['Completed', 'Jak Jones (WAL)', '4', '19', '10', 'Zhao Xintong (CHN)'],
//                    ['Completed', 'Mark Allen (NIR)', '10', '19', '6', 'Fan Zhengyi (CHN)'],
//                    ['Completed', 'John Higgins (SCT)', '10', '19', '7', 'Joe O\'Connor (ENG)'],
//                    ['Completed', 'Ding Junhui (CHN)', '10', '19', '7', 'Zak Surety (ENG)'],
//                    ['Completed', 'Si Jiahui (CHN)', '10', '19', '6', 'David Gilbert (ENG)'],
//                    ['Live', 'Shaun Murphy (ENG)', '7', '19', '2', 'Daniel Wells (WAL)'],
//                    ['Live', 'Zhang Anda (CHN)', '5', '19', '3', 'Pang Junxu (CHN)'],
//                    ['Scheduled', 'Luca Brecel (BEL)', '0', '19', '0', 'Ryan Day (WAL)'],
//                    ['Suspended', 'Ronnie O\'Sullivan (ENG)', '5', '19', '4', 'Ali Carter (ENG)'],
//                    ['Scheduled', 'Mark Selby (ENG)', '0', '19', '0', 'Ben Woollaston (ENG)'],
//                    ['Suspended', 'Judd Trump (ENG)', '6', '19', '3', 'Zhou Yuelong (CHN)'],
//                ]
//            )
            ->expectsOutputToContain('Round 2')
//            ->expectsTable(
//                [
//                    'Start',
//                    'Player One',
//                    'Points',
//                    'Frames',
//                    'Points',
//                    'Player Two',
//                ],
//                [
//                    ['Scheduled', 'Chris Wakelin (ENG)', '0', '0', '0', 'Mark Allen (NIR)'],
//                    ['Scheduled', 'John Higgins (SCT)', '0', '0', '0', 'Xiao Guodong (CHN)'],
//                    ['Scheduled', 'Hossein Vafaei (IRN)', '0', '0', '0', 'Mark Williams (WAL)'],
//                ]
//            )
            ->expectsOutputToContain('Last fetched: '.now()->timezone('Europe/Amsterdam')->format('H:i:s'));
    });
});
