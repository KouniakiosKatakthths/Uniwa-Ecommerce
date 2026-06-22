<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the default admin user from env variables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = env('ADMIN_EMAIL');
        $name  = env('ADMIN_NAME');
        $password = env('ADMIN_PASSWORD');

        if (!$name || !$email || !$password) 
            $this->warn('No ADMIN_NAME or ADMIN_EMAIL or ADMIN_PASSWORD set, Using defaults.');

        //Check if user exist
        $user = User::where('email', $email ?? 'admin@admin.com')->first();
        if ($user)
        {
            $this->info("Admin user already exists: {$user->email}");
            return;
        }

        $user = User::factory()->state([
            'name' => $name ?? 'admin',
            'email' => $email ?? 'admin@admin.com',
            'password' => Hash::make($password ?? 'password'),
        ])->as_admin()->create();

        $this->info("Admin user created: {$user->email}");
    }
}
