<?php

namespace App\Http\Controllers;

use App\Enums\MovieGenre;
use App\Enums\MovieRating;
use App\Models\Movie;
use App\Services\TmdbMovieImporter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Storage;

class MovieController extends Controller
{
    public function search(Request $request)
    {
        if ($id = $request->get('id')) 
            return response()->json(Movie::findOrFail($id, ['id', 'title']));
        

        $movies = Movie::query()
            ->where('title', 'LIKE', "%{$request->get('q', '')}%")
            ->orderBy('title')
            ->limit(10)
            ->get(['id', 'title']);

        return response()->json($movies);
    }

    public function index()
    {
        $movies = Movie::orderBy('created_at', 'desc')
            ->withCount('showtimes')
            ->paginate(15);

        return view('dashboard.list-movies', compact('movies'));
    }

    public function create()  
    {
        return view("dashboard.create-movie");
    } 

    public function store(Request $request, TmdbMovieImporter $tmdb)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'director'    => 'required|string',
            'rating'      => ['required', new Enum(MovieRating::class)],
            'duration'    => 'required|integer|min:1',
            'trailer_url' => 'required|url',
            'genre'       => ['required', new Enum(MovieGenre::class)],
            'release_date'=> 'required|date',
            'featured'    => 'boolean',
            'poster'      => 'required|image|max:2048',
            'actors'      => 'nullable|string',
            'tmdb_id' => 'nullable|integer',
        ]);

        if (!empty($data['actors']))
            $data['actors'] = array_map('trim', explode(',', $data['actors']));

        if ($request->hasFile('poster')) 
            $data['poster_url'] = $request->file('poster')->store('posters', 'public');
        
        unset($data['poster']); //Remove file field, we use poster_url instead
        $movie = Movie::create($data);

        if ($movie->tmdb_id)
            $tmdb->updateMovieVotes($movie, fn($msg) => logger($msg));

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

    public function edit(Movie $movie)
    {
        return view('dashboard.update-movie', compact('movie'));
    }

    
    public function update(Request $request, Movie $movie, TmdbMovieImporter $tmdb)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'director'     => 'required|string',
            'rating'       => ['required', new Enum(MovieRating::class)],
            'duration'     => 'required|integer|min:1',
            'trailer_url'  => 'required|url',
            'genre'        => ['required', new Enum(MovieGenre::class)],
            'release_date' => 'required|date',
            'poster'       => 'nullable|image|max:2048',    //Null on upgrade
            'actors'       => 'nullable|string',
            'tmdb_id' => 'nullable|integer',
        ]);

        $data['featured'] = $request->boolean('featured');

        if (!empty($data['actors']))
            $data['actors'] = array_map('trim', explode(',', $data['actors']));

        if ($request->hasFile('poster')) 
        {
            // Delete old poster
            if ($movie->poster_url)
                Storage::disk('public')->delete($movie->poster_url);

            $data['poster_url'] = $request->file('poster')->store('posters', 'public');
        }

        unset($data['poster']);
        $movie->update($data);

        if ($movie->tmdb_id)
            $tmdb->updateMovieVotes($movie, fn($msg) => logger($msg));

        return redirect()->route('movies.index')->with('success', 'Movie updated successfully.');
    }

    public function destroy(Movie $movie)
    {
        if ($movie->poster_url)
            Storage::disk('public')->delete($movie->poster_url);

        $movie->delete();

        return redirect()->route('movies.index')->with('success', 'Movie deleted successfully.');
    }
}
