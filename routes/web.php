<?php

use App\Http\Controllers\TransferMarketController;
use Illuminate\Support\Facades\Route;

Route::get('/', TransferMarketController::class)->name('home');

Route::get('/transfer-market', TransferMarketController::class)->name('transfer-market.index');
