<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Request;

class MovieController extends Controller
{
    public function index()   
    {
        $now_playing = Movie::inRandomOrder()->take(5)->get();
        
    }

    public function create()  
    {

    } 

    public function store()
    {

    }

    public function show(string $movie_id)
    {
        $movie = Movie::find($movie_id);
        $showtimes = $movie->showtimes()
            ->whereBetween("starts_at", [today("Europe/Athens"), today("Europe/Athens")->addDays(5)->endOfDay()])
            ->orderBy("starts_at")
            ->get()
            ->groupBy(fn($s) => $s->starts_at->toDateString());

        return view('movie', compact('movie', 'showtimes'));
    }

    public function now_playing(Request $request)
    {
        $now_playing = Movie::whereHas('showtimes', function ($query) {
                $query->where('starts_at', '>=', now("Europe/Athens"))->where('starts_at', '<=', now("Europe/Athens")->addDays(5));
            })
            ->when(request('search'), fn($q) => $q->where('title', 'like', '%'.request('search').'%'))
            ->when(request('day'), fn($q) => $q->whereHas('showtimes', fn($q) =>
                $q->whereDate('starts_at', request('day'))
            ))
            ->orderByDesc('featured')
            ->get();

        return view('now-playing', compact('now_playing'));
    }

    public function upcoming()
    {
        $upcoming = Movie::whereHas('showtimes', function ($query) {
                $query->where('starts_at', '>', now("Europe/Athens")->addDays(5));
            })
            ->when(request('search'), fn($q) => $q->where('title', 'like', '%'.request('search').'%'))
            ->orderByDesc('featured')
            ->get();

        return view('upcoming', compact('upcoming'));
    }

    public function edit()    {} // GET  /movies/{movie}/edit → edit form
    public function update()  {} // PUT  /movies/{movie}  → save changes
    public function destroy() {} // DELETE /movies/{movie} → delete
}
