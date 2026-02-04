<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // 👉 RUTA QUE TE FALTABA: /admin → redirige a dashboard
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        // Dashboard admin
        Route::get('/dashboard', fn () => view('admin.dashboard'))
            ->name('dashboard');

        // CRUD de productos
        Route::resource('products', ProductController::class);
    });
