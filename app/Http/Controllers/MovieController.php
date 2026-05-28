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

    public function show()    {} // GET  /movies/{movie}  → single movie
    public function edit()    {} // GET  /movies/{movie}/edit → edit form
    public function update()  {} // PUT  /movies/{movie}  → save changes
    public function destroy() {} // DELETE /movies/{movie} → delete
}
