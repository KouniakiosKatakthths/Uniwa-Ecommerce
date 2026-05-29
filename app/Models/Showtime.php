<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;

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

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
