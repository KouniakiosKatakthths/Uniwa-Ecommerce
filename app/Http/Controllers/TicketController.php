<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Showtime;
use DB;
use Exception;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Str;

class TicketController extends Controller
{
    public function create(Showtime $showtime)
    {
        $showtime->load('movie');

        $takenSeats = Ticket::where('showtime_id', $showtime->id)
            ->whereIn('status', [TicketStatus::Pending->value, TicketStatus::Confirmed->value])
            ->pluck('seat')
            ->toArray();

        return view('tickets.create-ticket', compact('showtime', 'takenSeats'));
    }

    public function store(Request $request, Showtime $showtime)
    {
        $request->validate([
            'seat' => ['required', 'string'],
        ]);

        //Transtaction to prevent race condition
        $ticket = DB::transaction(function () use ($showtime, $request) 
        {
            //Check if the requested seat is already taken
            $is_already_taken = Ticket::where('showtime_id', $showtime->id)
                ->where('seat', $request->seat)
                ->whereIn('status', [ TicketStatus::Pending, TicketStatus::Confirmed ])
                ->lockForUpdate()
                ->exists();

            if ($is_already_taken)
                throw new Exception("Requested seat is already taken");

            //Create new ticket
            return Ticket::create([
                'user_id'       => auth()->id(),
                'showtime_id'   => $showtime->id,
                'seat'          => $request->seat,
                'price'         => $showtime->ticket_price,
                'status'        => TicketStatus::Pending,
                'qr_code'       => Str::uuid(),
                'barcode'       => Str::upper(Str::random(10)),
            ]);
        });

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket booked!');
    }

    public function show(Ticket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);
        return view('tickets.show-ticket', compact('ticket'));
    }

    public function validateIndex()
    {
        return view('tickets.validate-ticket');
    }

    public function validateTicket(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = trim($request->code);

        // Try QR code first, then barcode
        $ticket = Ticket::with(['showtime.movie', 'user'])
            ->where('qr_code', $code)
            ->orWhere('barcode', $code)
            ->first();

        if (!$ticket) {
            return back()->with('error', 'Ticket not found.')->withInput();
        }

        if ($ticket->status === TicketStatus::Cancelled) {
            return back()->with('error', 'This ticket has been cancelled.')->withInput();
        }

        if ($ticket->status === TicketStatus::Confirmed) {
            return back()
                ->with('error', 'Ticket already used.')
                ->with('ticket', $ticket)
                ->withInput();
        }

        $ticket->update(['status' => TicketStatus::Confirmed]);

        return back()->with('success', 'Ticket validated!')->with('ticket', $ticket);
    }

    public function destroy(Ticket $ticket)
    {
        abort_unless(auth()->user()->isClerk(), 403);

        if ($ticket->status === TicketStatus::Confirmed) {
            return back()->with('error', 'Cannot cancel an already used ticket.');
        }

        $ticket->update(['status' => TicketStatus::Cancelled]);

        return back()->with('success', 'Ticket cancelled successfully.');
    }
}
