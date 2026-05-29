<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    public function show(string $showtime_id)
    {
        $showtime = Showtime::find($showtime_id);
        // return view("", compact(""));
    }
}
