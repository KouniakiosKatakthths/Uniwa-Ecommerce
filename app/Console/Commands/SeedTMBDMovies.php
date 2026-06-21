<?php

namespace App\Console\Commands;

use App\Services\TmdbMovieImporter;
use Illuminate\Console\Command;

class SeedTMBDMovies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmdb:seed-from-file {file=movie-seed-list.txt}';
    protected $description = 'Download movies info contained in a seed list from TMDB and seed the movies table';

    /**
     * Execute the console command.
     */
    public function handle(TmdbMovieImporter $importer): int
    {
        $path = storage_path($this->argument('file'));

        try 
        {
            $count = $importer->importFromFile($path, fn (string $message) => $this->line($message));
        } catch (\Throwable $e) 
        {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->info("Finished seeding {$count} movies from TMDB.");
        return self::SUCCESS;
    }
}
