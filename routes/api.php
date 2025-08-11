<?php

use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\KosController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PropertyOwnerController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/search-location', [LocationController::class, 'searchLocation']);
Route::get('/properties', [KosController::class, 'index']);
Route::get('/properties/{property}', [KosController::class, 'show']);
Route::post('/properties/{property}/reviews', [ReviewController::class, 'store']);
Route::post('/owner/register', [AuthController::class, 'register']);
Route::post('/owner/login', [AuthController::class, 'login']);
Route::get('/facilities', [FacilityController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/owner/properties', [PropertyOwnerController::class, 'index']);
    Route::post('/owner/properties', [PropertyOwnerController::class, 'store']);
    Route::put('/owner/properties/{property}', [PropertyOwnerController::class, 'update']);
    Route::delete('/owner/properties/{property}', [PropertyOwnerController::class, 'destroy']);
    Route::get('/user/profile', [UserController::class, 'show']);
    Route::put('/user/profile', [UserController::class, 'update']);
});