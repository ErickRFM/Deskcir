<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashierController;

Route::middleware(['auth', 'role:cashier,admin'])->group(function () {
    Route::get('/cashier', [CashierController::class, 'dashboard'])->name('cashier.dashboard');
    Route::get('/cashier/profile', [CashierController::class, 'profile'])->name('cashier.profile');
});
