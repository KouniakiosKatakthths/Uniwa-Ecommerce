<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Services\TmdbMovieImporter;
use Illuminate\Console\Command;

class UpdateMovieRatings extends Command
{
    protected $signature   = 'tmdb:update-ratings';
    protected $description = 'Fetch latest TMDB ratings for all movies';

    public function handle(TmdbMovieImporter $importer): int
    {
        $movies = Movie::whereNotNull('tmdb_id')->get();

        if ($movies->isEmpty()) 
        {
            $this->warn('No movies with a TMDB ID found.');
            return self::SUCCESS;
        }

        $this->info("Updating ratings for {$movies->count()} movies...");

        $bar = $this->output->createProgressBar($movies->count());
        $bar->start();

        $updated = 0;
        $failed  = 0;

        foreach ($movies as $movie) 
        {
            $result = $importer->updateMovieVotes(
                movie: $movie,
                logger: fn (string $msg) => $this->line(" {$msg}"),
            );

            $result['updated'] ? $updated++ : $failed++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! Updated: {$updated} | Skipped/Failed: {$failed}");

        return self::SUCCESS;
    }
}