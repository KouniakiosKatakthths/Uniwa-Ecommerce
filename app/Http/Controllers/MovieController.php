<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()   
    {
        $now_playing = Movie::inRandomOrder()->take(5)->get();
        
    }

    public function create()  
    {
        return view("dashboard.create-movie");
    } 

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'director'    => 'required|string',
            'rating'      => 'required|in:' . implode(',', Movie::RATINGS),
            'duration'    => 'required|integer|min:1',
            'trailer_url' => 'required|url',
            'genre'       => 'required|in:' . implode(',', Movie::GENRES),
            'release_date'=> 'required|date',
            'featured'    => 'required|boolean',
            'poster'      => 'required|image|max:2048',
            'actors'      => 'nullable|string',
        ]);

        if (!empty($data['actors']))
            $data['actors'] = array_map('trim', explode(',', $data['actors']));

        if ($request->hasFile('poster')) 
            $data['poster_url'] = $request->file('poster')->store('posters', 'public');
        
        unset($data['poster']); //Remove file field, we use poster_url instead
        Movie::create($data);

        return redirect()->route('movies.index')->with('success', 'Movie created successfully.');
    }

    public function show(Movie $movie)
    {
        $showtimes = $movie->showtimes()
            ->whereBetween("starts_at", [today("Europe/Athens"), today("Europe/Athens")->addDays(5)->endOfDay()])
            ->orderBy("starts_at")
            ->get()
            ->groupBy(fn($s) => $s->starts_at->toDateString());

        return view('movie', compact('movie', 'showtimes'));
    }

    public function edit()
    {
    }

    public function update()  {}

    public function destroy() {}
}
