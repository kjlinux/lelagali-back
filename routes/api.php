<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    require __DIR__ . '/routers/auth.php';
});

Route::prefix('app')->group(function () {
    require __DIR__ . '/routers/app.php';
});

Route::prefix('settings')->group(function () {
    require __DIR__ . '/routers/settings.php';
});
