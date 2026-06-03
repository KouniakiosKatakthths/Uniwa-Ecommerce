<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    public function index()   
    {
    }

    public function create()  
    {
        return view("dashboard.create-showtime");
    }

    public function store(Request $request)
    {
    }

    public function show(string $showtime_id)
    {
        $showtime = Showtime::find($showtime_id);
        // return view("", compact(""));
    }

    public function edit()
    {
    }

    public function update()
    {
    }

    public function destroy()
    {
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
}
