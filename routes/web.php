<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LeagueOverviewController;
use App\Http\Controllers\TransferMarketController;
use App\Http\Controllers\WaitlistSignupController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home/Index'))->name('home');
Route::post('/waitlist', WaitlistSignupController::class)->name('waitlist.store');

Route::middleware('guest')->group(function (): void {
	Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
	Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

	Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
	Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
	Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
	Route::get('/dashboard', fn () => Inertia::render('Dashboard/Index'))->name('dashboard');
	Route::get('/leagues', LeagueOverviewController::class)->name('leagues.index');
	Route::get('/transfer-market', TransferMarketController::class)->name('transfer-market.index');
});
