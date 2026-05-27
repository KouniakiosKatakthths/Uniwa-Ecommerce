<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'poster_url',
        'trailer_url',
        'duration',
        'rating',
        'score',
        'status',
        'featured',
        'release_date',
    ];

    protected $casts = [
        'release_date' => 'date',
        'featured' => 'boolean',
    ];

    //Helper to get duration as "2h 18m"
    public function getDurationFormatted(): string
    {
        return floor($this->duration / 60) . 'h ' . ($this->duration % 60) . 'm';
    }
}
