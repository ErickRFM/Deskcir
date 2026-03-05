<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HistoryController;

Route::middleware(['auth','role:client'])->group(function () {
    Route::get('/client', fn () => view('client.dashboard'));

    Route::get('/client/history', [HistoryController::class, 'index'])
        ->name('client.history');
});