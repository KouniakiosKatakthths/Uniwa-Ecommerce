<?php

namespace Database\Seeders;

use App\Enums\TicketStatus;
use App\Models\Showtime;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Str;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $users     = User::all();
        $showtimes = Showtime::all();

        if ($users->isEmpty() || $showtimes->isEmpty()) {
            $this->command->warn('No users or showtimes found. Run UserSeeder and ShowtimeSeeder first.');
            return;
        }

        foreach ($showtimes as $showtime) {
            // Pick a random number of seats to fill (up to 30% of capacity)
            $seatsToFill = rand(15, max(15, (int) ($showtime->total_seats * 0.6)));
            $takenSeats  = [];

            for ($i = 0; $i < $seatsToFill; $i++) {
                // Find a seat not already taken
                do {
                    $seat = rand(1, $showtime->total_seats);
                } while (in_array($seat, $takenSeats));

                $takenSeats[] = $seat;

                Ticket::create([
                    'user_id'     => $users->random()->id,
                    'showtime_id' => $showtime->id,
                    'seat'        => (string) $seat,
                    'price'       => $showtime->ticket_price,
                    'status'      => fake()->randomElement([
                        TicketStatus::Pending,
                        TicketStatus::Confirmed,
                        TicketStatus::Cancelled,
                    ]),
                    'qr_code'     => Str::uuid(),
                    'barcode'     => Str::upper(Str::random(10)),
                ]);
            }

            $this->command->info("Seeded {$seatsToFill} tickets for showtime {$showtime->id}");
        }
    }
}