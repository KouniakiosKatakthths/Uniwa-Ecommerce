<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(5)->create();

        User::factory()->count(1)->as_admin()->create();
        User::factory()->count(2)->as_clerk()->create();

        //Create one demo preset
        User::factory()->state([
            'name' => 'clerk',
            'email' => 'clerk@clerk.com',
            'password' => Hash::make('password2'),
        ])->as_clerk()->create();

        //Create demo preset
        User::factory()->state([
            'name' => 'user',
            'email' => 'user@user.com',
            'password' => Hash::make('password1'),
        ])->create();
    }
}
