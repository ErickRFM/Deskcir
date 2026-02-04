<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth','role:client'])->group(function () {
    Route::get('/client', fn () => view('client.dashboard'));
});
