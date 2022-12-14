<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Http::macro('ecoflow', function () {
            return Http::withHeaders([
                'User-Agent' => config('app.name').' ('
                                .config('mail.from.address').')',
            ])
                ->acceptJson()
                ->baseUrl('https://api.ecoflow.com/iot-service/open/api');
        });
    }
}
