<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;

// Routes d'authentification (sans middleware auth)
Route::post('login', [UserController::class, 'login']);
Route::post('users', [UserController::class, 'store']); // Inscription

// Routes protégées (avec middleware auth:api)
Route::middleware('auth:api')->group(function () {
    // Authentification - Accessible par tous les utilisateurs authentifiés
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('refresh', [UserController::class, 'refresh']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::put('users/{user}/profile', [UserController::class, 'updateProfile']);

    // Routes réservées aux admins uniquement
    Route::middleware('role:admin')->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{user}', [UserController::class, 'show']);
        Route::put('users/{user}', [UserController::class, 'update']);
        Route::put('users/{user}/reset-password', [UserController::class, 'resetPassword']);
        Route::put('users/{user}/role', [UserController::class, 'updateRole']);
        Route::delete('users/{user}', [UserController::class, 'destroy']);
        Route::get('users-trashed', [UserController::class, 'trashed']);
        Route::post('users/{id}/restore', [UserController::class, 'restore']);
    });
});
