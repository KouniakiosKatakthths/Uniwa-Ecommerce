<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    public function index(Request $request)   
    {
        $showtimes = Showtime::with('movie')
            ->when($request->day, fn($q) => $q->whereDate('starts_at', $request->day))
            ->when($request->room, fn($q) => $q->where('room', $request->room))
            ->when($request->filled('movie'), function ($query) use ($request) {
                $query->whereHas('movie', function ($movieQuery) use ($request) {
                    $movieQuery->where('title', 'like', '%' . $request->movie . '%');
                });
            })
            ->orderBy('starts_at', 'desc')
            ->paginate(20);

        $rooms = Showtime::distinct()->pluck('room');

        return view('dashboard.list-showtimes', compact('showtimes', 'rooms'));
    }

    public function create()  
    {
    return view("dashboard.create-showtime");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'movie_id'     => ['required', 'string', 'uuid', 'exists:movies,id'],
            'room'         => ['required', 'string', 'max:255'],
            'starts_at'    => ['required', 'date', 'after:now'],
            'ticket_price' => ['required', 'numeric', 'min:0'],
        ]);

        Showtime::create($data);

        return redirect()->route('showtimes.index')->with('success', 'Showtime created successfully.');
    }
    
    public function show(string $showtime_id)
    {
        $showtime = Showtime::find($showtime_id);
        // return view("", compact(""));
    }

    public function edit(Showtime $showtime)
    {
        return view('dashboard.update-showtime', compact('showtime'));
    }

    public function update(Request $request, Showtime $showtime)
    {
        $data = $request->validate([
            'movie_id'     => ['required', 'string', 'uuid', 'exists:movies,id'],
            'room'         => ['required', 'string', 'max:255'],
            'starts_at'    => ['required', 'date', 'after:now'],
            'ticket_price' => ['required', 'numeric', 'min:0'],
        ]);

        $showtime->update($data);
        return redirect()->route('showtimes.index')->with('success', 'Showtime updated successfully.');
    }

    public function destroy(Showtime $showtime)
    {
        $showtime->delete();
        return redirect()->route('showtimes.index')->with('success', 'Showtime deleted.');
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
            ->whereDoesntHave('showtimes', function ($query) {
                $query->whereBetween('starts_at', [
                    now("Europe/Athens"),
                    now("Europe/Athens")->addDays(5)->endOfDay()
                ]);
            })
            ->when(request('search'), fn($q) => $q->where('title', 'like', '%'.request('search').'%'))
            ->orderByDesc('featured')
            ->get();

        return view('upcoming', compact('upcoming'));
    }
}
