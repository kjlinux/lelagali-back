<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuartierController;
use App\Http\Controllers\MoyenPaiementController;

// Routes pour les quartiers
Route::get('quartiers', [QuartierController::class, 'index']);
Route::post('quartiers', [QuartierController::class, 'store']);
Route::get('quartiers/{quartier}', [QuartierController::class, 'show']);
Route::put('quartiers/{quartier}', [QuartierController::class, 'update']);
Route::delete('quartiers/{quartier}', [QuartierController::class, 'destroy']);
Route::get('quartiers-trashed', [QuartierController::class, 'trashed']);
Route::post('quartiers/{quartier}/restore', [QuartierController::class, 'restore']);

// Routes pour les moyens de paiement
Route::get('moyen-paiements', [MoyenPaiementController::class, 'index']);
Route::post('moyen-paiements', [MoyenPaiementController::class, 'store']);
Route::get('moyen-paiements/{moyenPaiement}', [MoyenPaiementController::class, 'show']);
Route::put('moyen-paiements/{moyenPaiement}', [MoyenPaiementController::class, 'update']);
Route::delete('moyen-paiements/{moyenPaiement}', [MoyenPaiementController::class, 'destroy']);
Route::get('moyen-paiements-trashed', [MoyenPaiementController::class, 'trashed']);
Route::post('moyen-paiements/{moyenPaiement}/restore', [MoyenPaiementController::class, 'restore']);
