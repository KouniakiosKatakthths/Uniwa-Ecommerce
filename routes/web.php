<?php

use App\Enums\UserRole;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
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

$clerk = UserRole::Clerk->value;  // 'clerk'
$admin = UserRole::Admin->value;  // 'admin'

//Publicly available routes
Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/newplaying', [ShowtimeController::class, "now_playing"])->name("movies.now");
Route::get("/upcoming", [ShowtimeController::class, "upcoming"])->name("movies.upcoming");
Route::get("/information", fn () => view("Information"))->name('info');
Route::get('/movies/search', [MovieController::class, 'search'])->name('movies.search');

// ======== Require clerk or admin operations ========
Route::middleware(['auth', "role:$clerk,$admin"])->group(function () {
    Route::resource('movies', MovieController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('showtimes', ShowtimeController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update']);

    Route::get('/validate',  [TicketController::class, 'validateIndex'])->name('tickets.validate');
    Route::post('/validate', [TicketController::class, 'validateTicket'])->name('tickets.validate.submit');

    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
});
// ======== Admim only operations ========
Route::middleware(['auth', "role:$admin"])->group(function () {
    Route::resource('movies', MovieController::class)->only(['destroy']);
    Route::resource('showtimes', ShowtimeController::class)->only(['destroy']);

    Route::get('/dashboard/users', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/dashboard/users/{user}/role', [ProfileController::class, 'updateRole'])->name('profile.update-role');
});
Route::resource('movies', MovieController::class)->only(['show']);

// ======== Require logged in user ======== 
Route::middleware('auth')->group(function () 
{
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// ======== Requre vefiried email for this operations ======== 
Route::middleware(['auth', 'verified'])->group(function () 
{
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/showtimes/{showtime}/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/showtimes/{showtime}/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
});

require __DIR__.'/auth.php';
