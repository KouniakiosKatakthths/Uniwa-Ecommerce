<?php

namespace App\Services;

use App\Enums\MovieGenre;
use App\Enums\MovieRating;
use App\Models\Movie;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TmdbMovieImporter
{
    private const BASE_URL    = 'https://api.themoviedb.org/3';
    private const IMAGE_URL   = 'https://image.tmdb.org/t/p/w500';
    private const YOUTUBE_URL = 'https://www.youtube.com/watch?v=';

    private PendingRequest $http;
    private \Closure $logger;

    public function __construct()
    {
        $token = config('services.tmdb.token');

        if (! $token) {
            throw new \RuntimeException('Missing TMDB_TOKEN in .env');
        }

        //Single configured HTTP client reused across all methods, retry 3x with 500ms delay on failure
        $this->http = Http::withToken($token)
            ->acceptJson()
            ->baseUrl(self::BASE_URL)
            ->retry(3, 500);   

        //Logs to Laravel log, replaced if caller provides one
        $this->logger = fn (string $msg) => Log::info('[TMDB] ' . $msg);
    }

    private function log(string $message): void
    {
        ($this->logger)($message);
    }

    private function withLogger(?callable $logger): static
    {
        if ($logger) $this->logger = $logger;
        return $this;
    }

    public function importFromFile(string $path, ?callable $logger = null): int
    {
        $this->withLogger($logger);

        if (! file_exists($path)) 
            throw new \RuntimeException("Movie seed file not found: {$path}");

        $titles = collect(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->unique()
            ->values();

        $count = 0;

        foreach ($titles as $title) 
        {
            $this->log("Searching TMDB for: {$title}");

            $movie = $this->importByTitle($title);

            if (! $movie) 
            {
                $this->log("No TMDB result found for: {$title}");
                continue;
            }

            $count++;
            $this->log("Saved: {$movie->title}");
        }

        return $count;
    }

    public function importByTitle(string $title, ?callable $logger = null): ?Movie
    {
        $this->withLogger($logger);

        $searchResult = $this->searchMovie($title);

        if (! $searchResult) return null;

        $tmdbId = $searchResult['id'];
        $details = $this->fetchMovieDetails($tmdbId);

        if (! $details) return null;

        [$credits, $trailerUrl, $certification] = [
            $this->fetchCredits($tmdbId),
            $this->fetchYoutubeTrailerUrl($tmdbId),
            $this->fetchCertification($tmdbId),
        ];

        return Movie::updateOrCreate(
            ['tmdb_id' => $details['id']],
            [
                'title'           => $details['title'] ?? $title,
                'description'     => $details['overview'] ?? null,
                'poster_url'      => $this->posterUrl($details['poster_path'] ?? null),
                'trailer_url'     => $trailerUrl,
                'release_date'    => $details['release_date'] ?: null,
                'duration'        => $details['runtime'] ?? null,
                'genre'           => $this->mapGenre($details['genres'][0]['name'] ?? null),
                'rating'          => $this->mapRating($certification),
                'featured'        => false,
                'director'        => $this->getDirectorName($credits),
                'actors'          => $this->getActors($credits, 10),
                'tmdb_rating'     => isset($details['vote_average'])
                                        ? round((float) $details['vote_average'], 1)
                                        : null,
                'tmdb_vote_count' => (int) ($details['vote_count'] ?? 0),
            ]
        );
    }

    public function updateMovieVotes(Movie $movie, ?callable $logger = null): array
    {
        $this->withLogger($logger);

        if (!$movie->tmdb_id) 
        {
            $this->log("Movie {$movie->title} does not have a TMDB ID.");
            return $this->voteResult($movie->tmdb_rating, $movie->tmdb_vote_count, false);
        }

        $this->log("Fetching latest TMDB votes for: {$movie->title}");

        $details = $this->fetchMovieDetails($movie->tmdb_id);

        if (! $details)
        {
            $this->log("Could not fetch TMDB details for: {$movie->title}");
            return $this->voteResult($movie->tmdb_rating, $movie->tmdb_vote_count, false);
        }

        $rating    = isset($details['vote_average']) ? round((float) $details['vote_average'], 1) : null;
        $voteCount = (int) ($details['vote_count'] ?? 0);

        $movie->update([
            'tmdb_rating'     => $rating,
            'tmdb_vote_count' => $voteCount,
        ]);

        $this->log("Updated {$movie->title}: {$rating}/10 from {$voteCount} votes.");

        return $this->voteResult($rating, $voteCount, true);
    }

    private function searchMovie(string $title): ?array
    {
        $response = $this->http->get('/search/movie', [
            'query'         => $title,
            'language'      => 'en-US',
            'include_adult' => false,
            'page'          => 1,
        ]);

        return $response->successful() ? $response->json('results.0') : null;
    }

    private function fetchMovieDetails(int $tmdbId): ?array
    {
        $response = $this->http->get("/movie/{$tmdbId}", [
            'language' => 'en-US',
        ]);

        return $response->successful() ? $response->json() : null;
    }

    private function fetchCredits(int $tmdbId): ?array
    {
        $response = $this->http->get("/movie/{$tmdbId}/credits", [
            'language' => 'en-US',
        ]);

        return $response->successful() ? $response->json() : null;
    }

    private function fetchYoutubeTrailerUrl(int $tmdbId): ?string
    {
        $response = $this->http->get("/movie/{$tmdbId}/videos", [
            'language' => 'en-US',
        ]);

        if ($response->failed()) return null;

        $videos = collect($response->json('results', []))
            ->where('site', 'YouTube');

        $video = $videos->where('type', 'Trailer')->where('official', true)->first()
            ?? $videos->where('type', 'Trailer')->first()
            ?? $videos->first();

        return ! empty($video['key']) ? self::YOUTUBE_URL . $video['key'] : null;
    }

    private function fetchCertification(int $tmdbId, string $country = 'US'): ?string
    {
        $response = $this->http->get("/movie/{$tmdbId}/release_dates");

        if ($response->failed()) return null;

        $countryRelease = collect($response->json('results', []))
            ->firstWhere('iso_3166_1', $country);

        if (! $countryRelease || empty($countryRelease['release_dates']))  return null;

        $releaseDate = collect($countryRelease['release_dates'])
            ->first(fn ($r) => ! empty($r['certification']));

        return $releaseDate['certification'] ?? null;
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function getDirectorName(?array $credits): ?string
    {
        if (! $credits || empty($credits['crew'])) return null;
        

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
        if (! $path) return null;

        $filename = 'posters/' . ltrim($path, '/');

        if (! Storage::disk('public')->exists($filename)) 
        {
            $response = Http::timeout(15)->get(self::IMAGE_URL . $path);

            if ($response->failed())
                return null;

            Storage::disk('public')->put($filename, $response->body());
        }

        return $filename;
    }

    private function voteResult(?float $rating, int $voteCount, bool $updated): array
    {
        return 
        [
            'tmdb_rating'     => $rating,
            'tmdb_vote_count' => $voteCount,
            'updated'         => $updated,
        ];
    }

    private function mapGenre(?string $genre): MovieGenre
    {
        return match ($genre) 
        {
            'Action'           => MovieGenre::Action,
            'Animation'        => MovieGenre::Animation,
            'Comedy'           => MovieGenre::Comedy,
            'Drama'            => MovieGenre::Drama,
            'Horror'           => MovieGenre::Horror,
            'Romance'          => MovieGenre::Romance,
            'Science Fiction'  => MovieGenre::Sci_Fi,
            'Thriller'         => MovieGenre::Thriller,
            'Documentary'      => MovieGenre::Documentary,
            'Fantasy'          => MovieGenre::Fantasy,
            default            => MovieGenre::Drama,
        };
    }

    private function mapRating(?string $rating): MovieRating
    {
        return match ($rating) 
        {
            'G'     => MovieRating::G,
            'PG'    => MovieRating::PG,
            'PG-13' => MovieRating::PG_13,
            'R'     => MovieRating::R,
            'NC-17' => MovieRating::NC_17,
            default => MovieRating::PG_13,
        };
    }
}