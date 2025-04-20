<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\System\AuthController;
use App\Http\Middleware\JwtMiddleware;

Route::prefix('/auth')->name('auth.')->group(function(){
    Route::post('/login', [AuthController::class, 'login'])->name('login.');
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
 });
