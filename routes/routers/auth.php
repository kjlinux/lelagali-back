<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;

// Routes d'authentification
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout']);
Route::post('refresh', [UserController::class, 'refresh']);

// Routes de gestion des utilisateurs
Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{user}', [UserController::class, 'show']);
Route::put('users/{user}', [UserController::class, 'update']);
Route::put('users/{user}/profile', [UserController::class, 'updateProfile']);
Route::put('users/{user}/reset-password', [UserController::class, 'resetPassword']);
Route::put('users/{user}/role', [UserController::class, 'updateRole']);
Route::delete('users/{user}', [UserController::class, 'destroy']);
Route::get('users-trashed', [UserController::class, 'trashed']);
Route::post('users/{id}/restore', [UserController::class, 'restore']);
Route::get('profile', [UserController::class, 'profile']);
