<?php

namespace Database\Seeders;

use App\Models\Showtime;
use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 10 movies with past showtimes (archived)
        Movie::factory()
            ->count(10)
            ->has(Showtime::factory()->count(5)->in_past())
            ->create();

        // 10 movies with future showtimes (upcoming)
        Movie::factory()
            ->count(10)
            ->has(Showtime::factory()->count(5)->in_future())
            ->create();

        // 10 movies with both past and future showtimes (now playing)
        Movie::factory()
            ->count(10)
            ->has(Showtime::factory()->count(3)->in_past())
            ->has(Showtime::factory()->count(5)->in_future())
            ->create();
    }
}
