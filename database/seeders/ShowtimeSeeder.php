<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShowtimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = ['Room 1', 'Room 2', 'Room 3', 'IMAX'];

        Movie::query()->each(function (Movie $movie) use ($rooms) {
            foreach (range(1, rand(2, 5)) as $i) {
                Showtime::create([
                    'movie_id' => $movie->id,
                    'room' => fake()->randomElement($rooms),
                    'starts_at' => now('Europe/Athens')
                        ->addDays(rand(0, 14))
                        ->setTime(fake()->randomElement([12, 15, 18, 21]), fake()->randomElement([0, 30])),
                    'ticket_price' => fake()->randomFloat(2, 5, 40),
                ]);
            }
        });
    }
}
