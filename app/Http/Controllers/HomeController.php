<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Movie::where('featured', true)->inRandomOrder()->first();
        $nowPlaying = Movie::where('status', 'now_playing')->inRandomOrder()->take(4)->get();

        return view("home", compact('featured', 'nowPlaying'));
    }
}
