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
        $email = config('app.admin_email');
        $name  = config('app.admin_name');
        $password = config('app.admin_password');

        if (!$name || !$email || !$password) 
            $this->warn('No ADMIN_NAME or ADMIN_EMAIL or ADMIN_PASSWORD set, Using defaults.');

        //Check if user exist
        $user = User::where('email', $email)->first();
        if ($user)
        {
            $this->info("Admin user already exists: {$user->email}");
            return;
        }

        $user = User::forceCreate([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => \App\Enums\UserRole::Admin,
        ]);

        $this->info("Admin user created: {$user->email}");
    }
}
