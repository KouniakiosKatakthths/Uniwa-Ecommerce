<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(MovieSeeder::class);
        $this->call(TmdbMovieSeeder::class);
        $this->call(ShowtimeSeeder::class);
        $this->call(UserSeeder::class);
    }
}
