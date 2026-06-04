<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'movie_id',
        'room',
        'starts_at',
        'ticket_price',
        'total_seats',
        'available_seats',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    // Relationship to movie
    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'id');
    }

    // Showtime model
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
