<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\MovieGenre;
use App\Enums\MovieRating;
use Storage;

class Movie extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'description',
        'poster_url',
        'trailer_url',
        'duration',
        'rating',
        'actors',
        'director',
        'genre',
        'featured',
        'release_date',
        'tmdb_id',
        'tmdb_rating',
        'tmdb_vote_count'
    ];

    protected $casts = [
        'actors' => 'array',
        'release_date' => 'date',
        'featured' => 'boolean',
        'rating' => MovieRating::class,
        'genre' => MovieGenre::class,
    ];

    //Helper to get duration as "2h 18m"
    public function getDurationFormatted(): string
    {
        return floor($this->duration / 60) . 'h ' . ($this->duration % 60) . 'm';
    }

    public function getMoviePoster(): string
    {
        return $this->poster_url !== null ?
            Storage::url($this->poster_url) :
            asset("images/movie_placeholder.jpg");
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'movie_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
