<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InviteController;

//public routes
Route::post('/auth/login', [AuthController::class, 'login']);
//protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('invites', InviteController::class);
    Route::put('invites/{id}/presence', [InviteController::class, 'updatePresence']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});