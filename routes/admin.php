<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TicketController;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // 👉 /admin → redirige a dashboard
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        // Dashboard admin
        Route::get('/dashboard', fn () => view('admin.dashboard'))
            ->name('dashboard');

        // CRUD de productos
        Route::resource('products', ProductController::class);

        // CRUD de usuarios
        Route::resource('users', UserController::class);

        // 🎫 TICKETS ADMIN
        Route::get('/tickets', [TicketController::class, 'index'])
            ->name('tickets.index');

        Route::get('/tickets/{id}', [TicketController::class, 'show'])
            ->name('tickets.show');

        Route::post('/tickets/{id}/status', [TicketController::class, 'updateStatus'])
            ->name('tickets.status');

        // 🔥 LO NUEVO QUE PEDISTE
        Route::post('/tickets/{id}/message',
            [TicketController::class,'addMessage']);

        Route::post('/tickets/{id}/assign',
            [TicketController::class,'assign']);
    });