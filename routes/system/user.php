<?php

use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\System\UserController;

// Định nghĩa route với middleware JwtMiddleware và SecretKeyMiddleware
Route::prefix('/user')->name('user.')
    ->middleware(JwtMiddleware::class)
    ->group(function () {

        // Thêm middleware secret.key cho từng route
        Route::get('list', [UserController::class, 'getAll'])
            ->middleware(['role.check:admin', 'secret.key']);

        Route::get('users', [UserController::class, 'index'])
            ->middleware(['role.check:staff', 'secret.key']);

        Route::get('users/{id}', [UserController::class, 'show'])
            ->middleware(['role.check:admin,traffic', 'secret.key']);

        Route::post('users', [UserController::class, 'store'])
            ->middleware('secret.key');

        Route::post('users/{id}', [UserController::class, 'update'])
            ->middleware(['secret.key']);

        Route::delete('users/{id}', [UserController::class, 'destroy'])
            ->middleware(['role.check:admin', 'secret.key']);
    });
