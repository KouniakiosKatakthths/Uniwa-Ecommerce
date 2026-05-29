<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'actors' => 'array',
        'release_date' => 'date',
        'featured' => 'boolean',
    ];

    //Helper to get duration as "2h 18m"
    public function getDurationFormatted(): string
    {
        return floor($this->duration / 60) . 'h ' . ($this->duration % 60) . 'm';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'movie_id', 'id');
    }

    // User model
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
