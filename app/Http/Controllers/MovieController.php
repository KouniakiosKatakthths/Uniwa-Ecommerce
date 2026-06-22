<?php

namespace App\Http\Controllers;

use App\Enums\MovieGenre;
use App\Enums\MovieRating;
use App\Models\Movie;
use App\Services\TmdbMovieImporter;
use Illuminate\Http\RedirectResponse;
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
            'tmdb_id'     => 'nullable|integer',
        ]);

        if (!empty($data['actors']))
            $data['actors'] = array_map('trim', explode(',', $data['actors']));

        if ($request->hasFile('poster')) 
            $data['poster_url'] = $request->file('poster')->store('posters', 'public');
        
        unset($data['poster']); //Remove file field, we use poster_url instead

        try
        {
            $movie = Movie::create($data);
            return $this->redirectWithTmdbUpdate($tmdb, $movie, 'Movie created successfully.');
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            //MySQL duplicate entry error code
            if ($e->errorInfo[1] === 1062) {
                return back()
                    ->withInput()
                    ->withErrors(['tmdb_id' => 'A movie with this TMDB ID already exists.']);
            }

            throw $e;
        }
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

        return $this->redirectWithTmdbUpdate($tmdb, $movie, 'Movie updated successfully.');
    }

    public function destroy(Movie $movie)
    {
        if ($movie->poster_url)
            Storage::disk('public')->delete($movie->poster_url);

        $movie->delete();

        return redirect()->route('movies.index')->with('success', 'Movie deleted successfully.');
    }

    public function tmdbLookup(Request $request, TmdbMovieImporter $tmdb)
    {
        $request->validate(['tmdb_id' => 'required|integer']);

        try 
        {
            $data = $tmdb->previewByTmdbId((int) $request->tmdb_id);
            if (!$data) return response()->json(['error' => 'No movie found with that TMDB ID.'], 404);

            return response()->json($data);
        } catch (\Exception $e) 
        {
            if ($e->getCode() == 404) return response()->json(['error'=> 'No movie found with that TMDB ID.'],404);
            return response()->json(['error' => "Internal server errror. Please try again later or nodify admin."], 500);
        }
    }

    private function redirectWithTmdbUpdate(TmdbMovieImporter $tmdb, Movie $movie, string $successMessage): RedirectResponse 
    {
        //Successful redirection
        $redirect = redirect()->route('movies.index')->with('success', $successMessage);
        if (!$movie->tmdb_id) return $redirect;

        try 
        {
            $tmdb->updateMovieVotes($movie, fn ($msg) => logger($msg));
        } catch (\Exception $e) 
        {
            //Redirect with tmdb error
            logger()->error("TMDB update failed for movie [{$movie->id}]: " . $e->getMessage());
            return redirect()->route('movies.edit', $movie)
                ->with('success', $successMessage)
                ->with('warning', 'TMDB ratings could not be fetched. Did you type the correct tmdb id?');
        }

        return $redirect;
    }
}
