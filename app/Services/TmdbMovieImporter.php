<?php

namespace App\Services;

use App\Enums\MovieGenre;
use App\Enums\MovieRating;
use App\Models\Movie;
use Illuminate\Support\Facades\Http;
use Storage;

class TmdbMovieImporter
{
    public function importFromFile(string $path, ?callable $logger = null): int
    {
        if (! file_exists($path)) {
            throw new \RuntimeException("Movie seed file not found: {$path}");
        }

        $titles = collect(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
            ->map(fn ($title) => trim($title))
            ->filter()
            ->unique()
            ->values();

        $count = 0;

        foreach ($titles as $title) {
            $logger("Searching TMDB for: {$title}");

            $movie = $this->importByTitle($title);

            if (! $movie) {
                $logger("No TMDB result found for: {$title}");
                continue;
            }

            $count++;
            $logger("Saved: {$movie->title}");
        }

        return $count;
    }

    public function updateMovieVotes(Movie $movie, ?callable $logger = null): array
    {
        $token = config('services.tmdb.token');

        if (! $token) {
            throw new \RuntimeException('Missing TMDB_TOKEN in .env');
        }

        if (! $movie->tmdb_id) {
            $logger("Movie {$movie->title} does not have a TMDB ID.");

            return [
                'tmdb_rating' => $movie->tmdb_rating,
                'tmdb_vote_count' => $movie->tmdb_vote_count,
                'updated' => false,
            ];
        }

        $logger("Fetching latest TMDB votes for: {$movie->title}");

        $details = $this->fetchMovieDetails($token, $movie->tmdb_id);

        if (! $details) {
            $logger("Could not fetch TMDB details for: {$movie->title}");

            return [
                'tmdb_rating' => $movie->tmdb_rating,
                'tmdb_vote_count' => $movie->tmdb_vote_count,
                'updated' => false,
            ];
        }

        $rating = isset($details['vote_average'])
            ? round((float) $details['vote_average'], 1)
            : null;

        $voteCount = isset($details['vote_count'])
            ? (int) $details['vote_count']
            : 0;

        $movie->update([
            'tmdb_rating' => $rating,
            'tmdb_vote_count' => $voteCount,
        ]);

        $logger("Updated {$movie->title}: {$rating}/10 from {$voteCount} votes.");

        return [
            'tmdb_rating' => $rating,
            'tmdb_vote_count' => $voteCount,
            'updated' => true,
        ];
    }
    public function importByTitle(string $title): ?Movie
    {
        $token = config('services.tmdb.token');

        if (! $token) {
            throw new \RuntimeException('Missing TMDB_TOKEN in .env');
        }

        $searchResult = $this->searchMovie($token, $title);

        if (! $searchResult) {
            return null;
        }

        $tmdbId = $searchResult['id'];

        $details = $this->fetchMovieDetails($token, $tmdbId);

        if (! $details) {
            return null;
        }

        $credits = $this->fetchCredits($token, $tmdbId);
        $trailerUrl = $this->fetchYoutubeTrailerUrl($token, $tmdbId);
        $certification = $this->fetchCertification($token, $tmdbId);

        return Movie::updateOrCreate(
            [
                'tmdb_id' => $details['id'],
            ],
            [
                'title' => $details['title'] ?? $title,
                'description' => $details['overview'] ?? null,
                'poster_url' => $this->posterUrl($details['poster_path'] ?? null),
                'trailer_url' => $trailerUrl,
                'release_date' => ! empty($details['release_date']) ? $details['release_date'] : null,
                'duration' => $details['runtime'] ?? null,
                'genre' => $this->mapGenre($details['genres'][0]['name'] ?? null),
                'rating' => $this->mapRating($certification),
                'featured' => false,
                'director' => $this->getDirectorName($credits),
                'actors' => $this->getActors($credits, 10),
                'tmdb_rating' => isset($details['vote_average'])
                    ? round((float) $details['vote_average'], 1)
                    : null,

                'tmdb_vote_count' => isset($details['vote_count'])
                    ? (int) $details['vote_count']
                    : 0,
            ]
        );
    }

    private function searchMovie(string $token, string $title): ?array
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->get('https://api.themoviedb.org/3/search/movie', [
                'query' => $title,
                'language' => 'en-US',
                'include_adult' => false,
                'page' => 1,
            ]);

        return $response->successful()
            ? $response->json('results.0')
            : null;
    }

    private function fetchMovieDetails(string $token, int $tmdbId): ?array
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->get("https://api.themoviedb.org/3/movie/{$tmdbId}", [
                'language' => 'en-US',
            ]);

        return $response->successful() ? $response->json() : null;
    }

    private function fetchCredits(string $token, int $tmdbId): ?array
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->get("https://api.themoviedb.org/3/movie/{$tmdbId}/credits", [
                'language' => 'en-US',
            ]);

        return $response->successful() ? $response->json() : null;
    }

    private function fetchYoutubeTrailerUrl(string $token, int $tmdbId): ?string
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->get("https://api.themoviedb.org/3/movie/{$tmdbId}/videos", [
                'language' => 'en-US',
            ]);

        if ($response->failed()) {
            return null;
        }

        $videos = collect($response->json('results', []))
            ->where('site', 'YouTube');

        $video = $videos
            ->where('type', 'Trailer')
            ->where('official', true)
            ->first()
            ?? $videos->where('type', 'Trailer')->first()
            ?? $videos->first();

        return ! empty($video['key'])
            ? 'https://www.youtube.com/watch?v=' . $video['key']
            : null;
    }

    private function fetchCertification(string $token, int $tmdbId, string $country = 'US'): ?string
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->get("https://api.themoviedb.org/3/movie/{$tmdbId}/release_dates");

        if ($response->failed()) {
            return null;
        }

        $countryRelease = collect($response->json('results', []))
            ->firstWhere('iso_3166_1', $country);

        if (! $countryRelease || empty($countryRelease['release_dates'])) {
            return null;
        }

        $releaseDate = collect($countryRelease['release_dates'])
            ->first(fn ($release) => ! empty($release['certification']));

        return $releaseDate['certification'] ?? null;
    }

    private function getDirectorName(?array $credits): ?string
    {
        if (! $credits || empty($credits['crew'])) {
            return null;
        }

        return collect($credits['crew'])
            ->where('job', 'Director')
            ->pluck('name')
            ->filter()
            ->implode(', ') ?: null;
    }

    private function getActors(?array $credits, int $limit = 10): array
    {
        if (! $credits || empty($credits['cast'])) {
            return [];
        }

        return collect($credits['cast'])
            ->take($limit)
            ->pluck('name')
            ->filter()
            ->values()
            ->all();
    }

    private function posterUrl(?string $path): ?string
    {
        if (!$path) return null;

        $tmdbUrl = 'https://image.tmdb.org/t/p/w500' . $path;
        return $this->downloadPoster($tmdbUrl, $path);
    }


    private function downloadPoster(string $url, string $tmdbPath): ?string
    {
        // Use the TMDB filename as the local filename
        $filename = 'posters/' . ltrim($tmdbPath, '/');

        // Skip download if already exists
        if (Storage::disk('public')->exists($filename)) {
            return Storage::disk('public')->url($filename);
        }

        $response = Http::timeout(15)->get($url);

        if ($response->failed()) {
            return null;
        }

        Storage::disk('public')->put($filename, $response->body());

        return Storage::disk('public')->url($filename);
    }

    private function mapGenre(?string $genre): MovieGenre
    {
        return match ($genre) {
            'Action' => MovieGenre::Action,
            'Animation' => MovieGenre::Animation,
            'Comedy' => MovieGenre::Comedy,
            'Drama' => MovieGenre::Drama,
            'Horror' => MovieGenre::Horror,
            'Romance' => MovieGenre::Romance,
            'Science Fiction' => MovieGenre::Sci_Fi,
            'Thriller' => MovieGenre::Thriller,
            'Documentary' => MovieGenre::Documentary,
            'Fantasy' => MovieGenre::Fantasy,
            default => MovieGenre::Drama,
        };
    }

    private function mapRating(?string $rating): MovieRating
    {
        return match ($rating) {
            'G' => MovieRating::G,
            'PG' => MovieRating::PG,
            'PG-13' => MovieRating::PG_13,
            'R' => MovieRating::R,
            'NC-17' => MovieRating::NC_17,
            default => MovieRating::PG_13,
        };
    }
}