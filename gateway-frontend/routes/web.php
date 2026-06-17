<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamPageController;
use App\Http\Controllers\PlayerPageController;
use App\Http\Controllers\MatchPageController;
use App\Http\Controllers\StatisticPageController;

// Public auth
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes (simple session check)
Route::middleware('gateway.auth')->group(function () {

})->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/teams', [TeamPageController::class, 'index']);
    Route::post('/teams', [TeamPageController::class, 'store']);
    Route::post('/teams/{id}/update', [TeamPageController::class, 'update']);
    Route::post('/teams/{id}/delete', [TeamPageController::class, 'delete']);

    Route::get('/players', [PlayerPageController::class, 'index']);
    Route::post('/players', [PlayerPageController::class, 'store']);
    Route::post('/players/{id}/update', [PlayerPageController::class, 'update']);
    Route::post('/players/{id}/delete', [PlayerPageController::class, 'delete']);

    Route::get('/matches', [MatchPageController::class, 'index']);
    Route::post('/matches', [MatchPageController::class, 'store']);
    Route::post('/matches/{id}/update', [MatchPageController::class, 'update']);
    Route::post('/matches/{id}/delete', [MatchPageController::class, 'delete']);

    Route::get('/statistics', [StatisticPageController::class, 'index']);
    Route::post('/statistics', [StatisticPageController::class, 'store']);
    Route::post('/statistics/{id}/update', [StatisticPageController::class, 'update']);
    Route::post('/statistics/{id}/delete', [StatisticPageController::class, 'delete']);

    // (Inventories and Inventory Requests pages removed)
});

Route::get('/', function () {
    return redirect('/login');
});
