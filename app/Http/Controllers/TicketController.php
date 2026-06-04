<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use DB;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function create(Showtime $showtime)
    {
        return view('tickets.create', compact('showtime'));
    }

    public function store(Request $request, Showtime $showtime)
    {
        $request->validate([
            'seat' => ['required', 'string'],
        ]);

        $ticket = DB::transaction(function () use ($showtime, $request) {
            $is_already_taken = Ticket::where('showtime_id', $showtime->id)
                ->where('seat', $request->seat)
                ->whereIn('status');
        });
    }
}
