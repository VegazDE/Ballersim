<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Leagues\DivisionShowController;
use App\Http\Controllers\Leagues\LeagueShowController;
use App\Http\Controllers\LeagueOverviewController;
use App\Http\Controllers\PlayerProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TeamProfileController;
use App\Http\Controllers\TeamShowController;
use App\Http\Controllers\TransferMarketController;
use App\Http\Controllers\WaitlistSignupController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home/Index'))->name('home');
Route::post('/waitlist', WaitlistSignupController::class)->name('waitlist.store');

Route::middleware('guest')->group(function (): void {
	Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
	Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::redirect('/register', '/')->name('register');

Route::middleware('auth')->group(function (): void {
	Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
	Route::get('/dashboard', DashboardController::class)->name('dashboard');
	Route::get('/my-team', TeamProfileController::class)->name('team.profile');
	Route::get('/my-team/players/{player}', PlayerProfileController::class)->name('player.profile');
	Route::get('/leagues', LeagueOverviewController::class)->name('leagues.index');
	Route::get('/leagues/{league}', LeagueShowController::class)->name('leagues.show');
	Route::get('/leagues/{league}/divisions/{division}', DivisionShowController::class)->name('divisions.show');
	Route::get('/teams/{team}', TeamShowController::class)->name('teams.show');
	Route::get('/spielplan', ScheduleController::class)->name('schedule.index');
	Route::get('/transfer-market', TransferMarketController::class)->name('transfer-market.index');
});
