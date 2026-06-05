<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Services\TmdbMovieImporter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TmdbMovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('movies_for_seeding.txt');

        try {
            $count = app(TmdbMovieImporter::class)->importFromFile($path, function (string $message) {
                $this->command?->line($message);
            });

            $this->command?->info("Imported {$count} movies from TMDB.");

            Movie::query()->update([
                'featured' => false,
            ]);

            Movie::query()
                ->inRandomOrder()
                ->limit(4)
                ->update([
                    'featured' => true,
                ]);

            $this->command?->info('Selected 4 random featured movies.');
        } catch (\Throwable $e) {
            $this->command?->error($e->getMessage());
        }
    }
}
