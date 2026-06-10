<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()
            ->tickets()
            ->with(['showtime.movie'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('dashboard.dashboard', compact('tickets'));
    }
}
