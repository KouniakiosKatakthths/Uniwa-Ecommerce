<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NowPlayingController extends Controller
{
    public function index()
    {
        return view("now-playing");
    }
}
