<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth','role:technician'])->group(function () {
    Route::get('/technician', fn () => view('technician.dashboard'));
});
