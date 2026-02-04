<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('public.home'));

// 🛒 STORE
Route::get('/store', [StoreController::class, 'index']);
Route::get('/store/category/{slug}', [StoreController::class, 'category']);
Route::get('/store/product/{id}', [StoreController::class, 'show']);
Route::get('/cart', [StoreController::class, 'cart']);
Route::post('/cart/add/{id}', [StoreController::class, 'addToCart']);

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 🔐 Auth
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/support', [TicketController::class, 'create']);
    Route::post('/support', [TicketController::class, 'store']);

    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::post('/tickets/{id}/message', [TicketController::class, 'addMessage']);

    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/create/{ticket}', [AppointmentController::class, 'create']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);

    Route::get('/reports/{ticket}', [ReportController::class, 'show']);
    Route::get('/reports/{ticket}/pdf', [ReportController::class, 'pdf']);
});

require __DIR__ . '/admin.php';
require __DIR__ . '/client.php';
require __DIR__ . '/technician.php';
require __DIR__ . '/auth.php';
require __DIR__.'/store.php';