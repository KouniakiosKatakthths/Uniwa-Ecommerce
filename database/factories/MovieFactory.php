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
            'poster_url' => "https://www.google.com/imgres?q=movie%20image&imgurl=https%3A%2F%2Fimg.magnific.com%2Fpremium-photo%2Fflying-popcorn-3d-glasses-film-reel-clapboard-yellow-background-cinema-movie-concept-3d_989822-1302.jpg%3Fsemt%3Dais_hybrid%26w%3D740%26q%3D80&imgrefurl=https%3A%2F%2Fwww.magnific.com%2Ffree-photos-vectors%2Fmovies&docid=qkSncCrt38f5HM&tbnid=SgsSWy1hyH68TM&vet=12ahUKEwiNlsr2ztqUAxXUavEDHZUDJTUQnPAOegQIGRAB..i&w=740&h=493&hcb=2&ved=2ahUKEwiNlsr2ztqUAxXUavEDHZUDJTUQnPAOegQIGRAB",
            'trailer_url' => "https://www.youtube.com/watch?v=06gXGAHnRyE",
            'duration' => fake()->numberBetween(90, 180),
            'rating' => fake()->randomElement(['PG', 'PG-13', 'R']),
            'status' => fake()->randomElement(['now_playing', 'coming_soon']),
            'featured' => false,
            'release_date' => fake()->dateTime($max = 'now', $timezone = null),
        ];
    }
}
