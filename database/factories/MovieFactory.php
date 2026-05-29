<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'poster_url' => "https://img.magnific.com/free-vector/cinema-movie-entertainment-poster_98292-1670.jpg?semt=ais_hybrid&w=740&q=80",
            'trailer_url' => "https://www.youtube.com/watch?v=06gXGAHnRyE",
            'duration' => fake()->numberBetween(90, 180),
            'rating' => fake()->randomElement(['PG', 'PG-13', 'R']),
            'actors' => [ fake()->words(2, true), fake()->words(2, true), fake()->words(2, true) ],
            'director' => fake()->words(2, true),
            'genre' => fake()->word(),
            'featured' => fake()->boolean(),
            'release_date' => fake()->dateTime($max = 'now', $timezone = null),
        ];
    }

    public function featured(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'featured' => true,
            ];
        });
    }
}
