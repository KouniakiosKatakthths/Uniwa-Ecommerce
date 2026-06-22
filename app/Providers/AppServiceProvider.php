<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Enforce HTTPS
        if (env('FORCE_HTTPS', false))
            URL::forceScheme('https');

        # Root path on prod
        if (config('app.env') === 'production') 
            URL::forceRootUrl(config('app.url'));
    }
}
