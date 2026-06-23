<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

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
        if (config('app.force_https'))
            URL::forceScheme('https');

        # Root path on prod
        if (config('app.env') === 'production') 
            URL::forceRootUrl(config('app.url'));

        Paginator::currentPathResolver(function () {
            return rtrim(config('app.url'), '/') . '/' . ltrim(request()->path(), '/');
        });
    }
}
