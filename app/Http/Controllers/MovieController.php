<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()   
    {
        $now_playing = Movie::where("status", "now_playing")->get();
        $comingSoon = Movie::where('status', 'coming_soon')->get();
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
        return view('movie', compact('movie'));
    }

    public function now_playing()
    {
        return view('now-playing');
    }

    public function upcoming()
    {
        return view('upcoming');
    }

    public function edit()    {} // GET  /movies/{movie}/edit → edit form
    public function update()  {} // PUT  /movies/{movie}  → save changes
    public function destroy() {} // DELETE /movies/{movie} → delete
}
