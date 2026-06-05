<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Movie::where('featured', true)
            ->whereHas('showtimes', function ($query) {
                $query->where('starts_at', '>=', now('Europe/Athens'));
            })
            ->with(['showtimes' => function ($query) {
                $query
                    ->where('starts_at', '>=', now('Europe/Athens'))
                    ->orderBy('starts_at');
            }])
            ->inRandomOrder()
            ->first();

        $nowPlaying = Movie::whereHas('showtimes', function ($query) {
                $query->whereBetween('starts_at', [
                    now('Europe/Athens'),
                    now('Europe/Athens')->addDays(5),
                ]);
            })
            ->with(['showtimes' => function ($query) {
                $query
                    ->whereBetween('starts_at', [
                        now('Europe/Athens'),
                        now('Europe/Athens')->addDays(5),
                    ])
                    ->orderBy('starts_at');
            }])
            ->orderByDesc('featured')
            ->take(4)
            ->get();

        return view('home', compact('featured', 'nowPlaying'));
    }
}
