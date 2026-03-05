<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Technician\TechnicianController;

Route::middleware(['auth','role:technician'])->group(function () {
    Route::get('/technician', [TechnicianController::class, 'dashboard'])
        ->name('technician.dashboard');
});
