<?php
use App\Http\Controllers\StoreController;

Route::get('/store', [StoreController::class, 'index'])
    ->name('store.index');
