<?php

namespace App\Providers;

use App\Exceptions\WstRequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::macro('wst', function (string $subdomain) {
            return Http::baseUrl(sprintf('https://%s.snooker.web.gc.wstservices.co.uk/v2', $subdomain))
                ->throw(fn (Response $response) => throw new WstRequestException($response));
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
