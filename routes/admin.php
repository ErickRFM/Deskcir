<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
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

        // Dashboard admin (AHORA SÍ USA EL CONTROLADOR)
        Route::get('/dashboard', [AdminController::class,'dashboard'])
            ->name('dashboard');

        // CRUD productos
        Route::resource('products', ProductController::class);

        // CRUD usuarios
        Route::resource('users', UserController::class);

        // 🎫 TICKETS ADMIN
        Route::get('/tickets', [TicketController::class, 'index'])
            ->name('tickets.index');

        Route::get('/tickets/{id}', [TicketController::class, 'show'])
            ->name('tickets.show');

        Route::post('/tickets/{id}/status', [TicketController::class, 'updateStatus'])
            ->name('tickets.status');

        // 💬 responder ticket
        Route::post('/tickets/{id}/message',
            [TicketController::class,'addMessage'])
            ->name('tickets.message');

        // 👨‍🔧 asignar técnico
        Route::post('/tickets/{id}/assign',
            [TicketController::class,'assign'])
            ->name('tickets.assign');
    });