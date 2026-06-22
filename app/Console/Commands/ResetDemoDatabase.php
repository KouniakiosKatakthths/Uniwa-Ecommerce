<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ResetDemoDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and reseed the database in demo mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!config('app.demo_mode')) 
        {
            $this->info('Demo mode is disabled.');
            return self::SUCCESS;
        }

        $this->warn('Resetting demo database...');

        Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]);

        $this->line(Artisan::output());
        Artisan::call('admin:create');

        $this->line(Artisan::output());
        $this->info('Demo database reset, reseeded, and admin user created.');

        return self::SUCCESS;
    }
}
