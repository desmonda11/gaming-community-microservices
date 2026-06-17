<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\StatisticController;
// InventoryController removed

Route::middleware('jwt.auth')->group(function () {
    // Teams
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::put('/teams/{id}', [TeamController::class, 'update']);
    Route::delete('/teams/{id}', [TeamController::class, 'destroy']);

    // Players
    Route::get('/players', [PlayerController::class, 'index']);
    Route::post('/players', [PlayerController::class, 'store']);
    Route::put('/players/{id}', [PlayerController::class, 'update']);
    Route::delete('/players/{id}', [PlayerController::class, 'destroy']);

    // Matches
    Route::get('/matches', [MatchController::class, 'index']);
    Route::get('/matches/{id}', [MatchController::class, 'show']);
    Route::post('/matches', [MatchController::class, 'store']);
    Route::put('/matches/{id}', [MatchController::class, 'update']);
    Route::delete('/matches/{id}', [MatchController::class, 'destroy']);

    // Statistics
    Route::get('/statistics', [StatisticController::class, 'index']);
    Route::post('/statistics', [StatisticController::class, 'store']);
    Route::put('/statistics/{id}', [StatisticController::class, 'update']);
    Route::delete('/statistics/{id}', [StatisticController::class, 'destroy']);

    // (Inventories and Inventory Requests feature removed)
});
