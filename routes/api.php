<?php

use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\KosController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/search-location', [LocationController::class, 'searchLocation']);
Route::get('/kos', [KosController::class, 'index']);
Route::get('/kos/{kos}', [KosController::class, 'show']);
Route::post('/kos/{kos}/reviews', [ReviewController::class, 'store']);
Route::post('/owner/register', [AuthController::class, 'register']);
Route::post('/owner/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/owner/kos', [OwnerController::class, 'index']);
    Route::post('/owner/kos', [OwnerController::class, 'store']);
    Route::put('/owner/kos/{kos}', [OwnerController::class, 'update']);
    Route::delete('/owner/kos/{kos}', [OwnerController::class, 'destroy']);
    Route::get('/user/profile', [UserController::class, 'show']);
    Route::put('/user/profile', [UserController::class, 'update']);
});
