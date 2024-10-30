<?php

namespace App\Services;

use App\Dtos\Rankings;
use App\Factories\RankingsFactory;
use Illuminate\Support\Facades\Http;

class RankingsService
{
    public function __construct(private RankingsFactory $rankingsFactory) {}

    public function getRankings(): Rankings
    {
        $response = Http::get('https://rankings.snooker.web.gc.wstservices.co.uk/v2', [
            'rankingsLimit' => 128,
            'showOfficial' => true,
        ]);

        return $this->rankingsFactory->createRankingsFromResponse($response);
    }
}
