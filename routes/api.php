<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\AppointmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:customer'])->group(function (){
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'role:provider'])->group(function (){
    Route::get('/providers', [ProviderController::class, 'index']);
    Route::get('/providers/{provider}', [ProviderController::class, 'show']);
});