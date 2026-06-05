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
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('dashboard.dashboard', compact('tickets'));
    }
}
