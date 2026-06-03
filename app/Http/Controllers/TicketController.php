<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function purchase_for_showtime()
    {
        return view("purchase-ticket");
    }
}
