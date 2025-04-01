<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\System\DriverController;

Route::prefix('/driver')->name('driver.')
    ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::get('/list', [DriverController::class, 'getAll'])
            ->middleware('role.check:admin');

        Route::get('drivers', [DriverController::class, 'index'])
            ->middleware('role.check:staff,admin');

        Route::get('driver/{id}', [DriverController::class, 'show'])
            ->middleware('role.check:admin,traffic');

        Route::post('driver', [DriverController::class, 'store'])
            ->middleware('role.check:admin');

        Route::put('driver/{id}', [DriverController::class, 'update'])
            ->middleware('role.check:admin');

        Route::delete('driver/{id}', [DriverController::class, 'destroy'])
            ->middleware('role.check:admin');
    });
