<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/newplaying', [ShowtimeController::class, "now_playing"])->name("movies.now");
Route::get("/upcoming", [ShowtimeController::class, "upcoming"])->name("movies.upcoming");
Route::get("/information", fn () => view("Information"))->name('info');

Route::middleware(['auth', 'role:clerk,admin'])->group(function () {
    Route::resource('movies', MovieController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('showtimes', ShowtimeController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('movies', MovieController::class)->only(['destroy']);
});
Route::resource('movies', MovieController::class)->only(['show']);

Route::get('/dashboard', fn () => view('dashboard.dashboard'))
->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () 
{
    Route::get('/movie/{movie_id}/ticket', [TicketController::class, "purchase_for_showtime"])->name("movie.ticket");

    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
