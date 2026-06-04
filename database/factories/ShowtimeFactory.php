<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Showtime>
 */
class ShowtimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room' => $this->faker->randomElement([ 'Room 1', 'Room 2', 'Room 3' ]),
            'starts_at' => $this->faker->dateTimeThisYear('now', null),
            'ticket_price' => $this->faker->numberBetween(10, 50),
            'total_seats' => 100,
        ];
    }

    public function in_past(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'starts_at' => $this->faker->dateTimeBetween('-1 years', '-1 days'),
            ];
        });
    }

    public function in_present(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'starts_at' => $this->faker->dateTime(),
            ];
        });
    }

    public function in_future(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'starts_at' => $this->faker->dateTimeBetween('now', '+6 months'),
            ];
        });
    }
}
