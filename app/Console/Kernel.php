<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //Runs every six hours
        $schedule->command('tmdb:update-ratings')
            ->everySixHours()                           
            ->withoutOverlapping()              // skip if previous run is still going
            ->runInBackground()                 // don't block other scheduled tasks
            ->appendOutputTo(storage_path('logs/tmdb-ratings.log'));  // log output

        //Register demo command
        $schedule->command('demo:reset')
            ->daily()
            ->when(fn () => config('app.demo_mode'))
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/demo-reset.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
