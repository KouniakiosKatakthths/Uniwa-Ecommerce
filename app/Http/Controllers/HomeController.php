<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Movie::where('featured', true)->inRandomOrder()->first();
        $nowPlaying = Movie::inRandomOrder()->take(5)->get();

        return view("home", compact('featured', 'nowPlaying'));
    }
}
