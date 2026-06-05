<?php

namespace App\Console\Commands;

use App\Enums\MovieGenre;
use App\Enums\MovieRating;
use App\Models\Movie;
use App\Services\TmdbMovieImporter;
use Http;
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

        try {
            $count = $importer->importFromFile($path, function (string $message) {
                $this->line($message);
            });
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->info("Finished seeding {$count} movies from TMDB.");

        return self::SUCCESS;
    }
    // public function handle()
    // {
    //     //Get TMBD access token
    //     $token = config('services.tmdb.token');
    //     if (!$token) 
    //     {
    //         $this->error('Missing TMDB_TOKEN in .env');
    //         return self::FAILURE;
    //     }

    //     $path = storage_path($this->argument('file'));

    //     //Get the movie list
    //     if (!file_exists($path)) 
    //     {
    //         $this->error("File not found: {$path}");
    //         return self::FAILURE;
    //     }

    //     //Load movie list
    //     $titles = collect(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
    //         ->map(fn ($title) => trim($title))
    //         ->filter()
    //         ->unique()
    //         ->values();

    //     if ($titles->isEmpty()) 
    //     {
    //         $this->warn('No movie titles found in file.');
    //         return self::SUCCESS;
    //     }

    //     foreach ($titles as $title) 
    //     {
    //         $this->info("Searching TMDB for: {$title}");
    //         $searchResult = $this->searchMovie($token, $title);

    //         if (!$searchResult) 
    //         {
    //             $this->warn("No TMDB result found for: {$title}");
    //             continue;
    //         }

    //         $details = $this->fetchMovieDetails($token, $searchResult['id']);

    //         if (!$details) 
    //         {
    //             $this->warn("Could not fetch details for: {$title}");
    //             continue;
    //         }

    //         $credits = $this->fetchMovieCredits($token, $searchResult["id"]);
    //         if (!$details) 
    //         {
    //             $this->warn("Could not fetch credits for: {$title}");
    //             continue;
    //         }

    //         $director = $this->getDirectorName($credits);
    //         $actors = $this->getActors($credits);
    //         $yt_trailer = $this->fetchYoutubeTrailerUrl($token, $searchResult["id"]);
    //         $cert = $this->fetchCertification($token, $searchResult["id"]);

    //         Movie::updateOrCreate(
    //             [
    //                 'tmdb_id' => $details['id'],
    //             ],
    //             [
    //                 'title' => $details['title'] ?? $title,
    //                 'description' => $details['overview'] ?? null,
    //                 'poster_url' => $this->posterUrl($details['poster_path'] ?? null),
    //                 'trailer_url' => $yt_trailer,
    //                 'release_date' => !empty($details['release_date']) ? $details['release_date'] : null,
    //                 'duration' => $details['runtime'] ?? null,
    //                 'genre' => $this->mapGenre($details['genres'][0]['name'] ?? null),
    //                 'rating' => $this->mapRating($cert),
    //                 'featured' => false,
    //                 'director' => $director,
    //                 'actors' => $actors,
    //             ]
    //         );

    //         $this->line('Saved: ' . ($details['title'] ?? $title));
    //     }

    //     $this->info('Finished seeding movies from file.');
    //     return self::SUCCESS;
    // }

    // private function searchMovie(string $token, string $title): ?array
    // {
    //     $response = Http::withToken($token)
    //         ->acceptJson()
    //         ->get('https://api.themoviedb.org/3/search/movie', [
    //             'query' => $title,
    //             'language' => 'en-US',
    //             'include_adult' => true,
    //             'page' => 1,
    //         ]);

    //     if ($response->failed()) 
    //     {
    //         return null;
    //     }

    //     return $response->json('results.0');
    // }

    // private function fetchMovieDetails(string $token, int $tmdbId): ?array
    // {
    //     $response = Http::withToken($token)
    //         ->acceptJson()
    //         ->get("https://api.themoviedb.org/3/movie/{$tmdbId}", [
    //             'language' => 'en-US',
    //         ]);

    //     if ($response->failed()) {
    //         return null;
    //     }

    //     return $response->json();
    // }

    // private function fetchMovieCredits(string $token, int $tmdbId): ?array
    // {
    //     $response = Http::withToken($token)
    //         ->acceptJson()
    //         ->get("https://api.themoviedb.org/3/movie/{$tmdbId}/credits", [
    //             'language' => 'en-US',
    //         ]);

    //     if ($response->failed()) {
    //         return null;
    //     }

    //     return $response->json();
    // }

    // private function getDirectorName(?array $credits): ?string
    // {
    //     if (! $credits || empty($credits['crew'])) {
    //         return null;
    //     }

    //     $director = collect($credits['crew'])
    //         ->firstWhere('job', 'Director');

    //     return $director['name'] ?? null;
    // }

    // private function getActors(?array $credits, int $limit = 5): array
    // {
    //     if (! $credits || empty($credits['cast'])) {
    //         return [];
    //     }

    //     return collect($credits['cast'])
    //         ->take($limit)
    //         ->pluck('name')
    //         ->filter()
    //         ->values()
    //         ->all();
    // }

    // private function fetchYoutubeTrailerUrl(string $token, int $tmdbId): ?string
    // {
    //     $response = Http::withToken($token)
    //         ->acceptJson()
    //         ->get("https://api.themoviedb.org/3/movie/{$tmdbId}/videos", [
    //             'language' => 'en-US',
    //         ]);

    //     if ($response->failed()) {
    //         return null;
    //     }

    //     $video = collect($response->json('results', []))
    //         ->first(function ($video) {
    //             return ($video['site'] ?? null) === 'YouTube'
    //                 && ($video['type'] ?? null) === 'Trailer';
    //         });

    //     if (! $video || empty($video['key'])) {
    //         return null;
    //     }

    //     return 'https://www.youtube.com/watch?v=' . $video['key'];
    // }

    // private function fetchCertification(string $token, int $tmdbId, string $country = 'US'): ?string
    // {
    //     $response = Http::withToken($token)
    //         ->acceptJson()
    //         ->get("https://api.themoviedb.org/3/movie/{$tmdbId}/release_dates");

    //     if ($response->failed()) {
    //         return null;
    //     }

    //     $countryRelease = collect($response->json('results', []))
    //         ->firstWhere('iso_3166_1', $country);

    //     if (! $countryRelease || empty($countryRelease['release_dates'])) {
    //         return null;
    //     }

    //     $releaseDate = collect($countryRelease['release_dates'])
    //         ->first(fn ($release) => ! empty($release['certification']));

    //     return $releaseDate['certification'] ?? null;
    // }

    // private function posterUrl(?string $path): ?string
    // {
    //     return $path ? 'https://image.tmdb.org/t/p/w500' . $path : null;
    // }

    // private function mapGenre(?string $genre): MovieGenre
    // {
    //     return match ($genre) {
    //         'Action' => MovieGenre::Action,
    //         'Animation' => MovieGenre::Animation,
    //         'Comedy' => MovieGenre::Comedy,
    //         'Drama' => MovieGenre::Drama,
    //         'Horror' => MovieGenre::Horror,
    //         'Romance' => MovieGenre::Romance,
    //         'Science Fiction' => MovieGenre::Sci_Fi,
    //         'Thriller' => MovieGenre::Thriller,
    //         'Documentary' => MovieGenre::Documentary,
    //         'Fantasy' => MovieGenre::Fantasy,
    //         default => MovieGenre::Drama,
    //     };
    // }

    // private function mapRating(?string $rating): MovieRating
    // {
    //     return match ($rating) {
    //         'G' => MovieRating::G,
    //         'PG' => MovieRating::PG,
    //         'PG-13' => MovieRating::PG_13,
    //         'R' => MovieRating::R,
    //         'NC-17' => MovieRating::NC_17,
    //         default => MovieRating::PG_13,
    //     };
    // }
}
