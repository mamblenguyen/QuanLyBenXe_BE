<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\System\GarageController;

Route::prefix('/garage')->name('garage.')
    ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::get('/list', [GarageController::class, 'getAll'])
            ->middleware('role.check:admin');

        Route::get('garages', [GarageController::class, 'index'])
            ->middleware('role.check:staff,admin');

        Route::get('garage/{id}', [GarageController::class, 'show'])
            ->middleware('role.check:admin,traffic');

        Route::post('garage', [GarageController::class, 'store'])
            ->middleware('role.check:admin');

        Route::put('garage/{id}', [GarageController::class, 'update'])
            ->middleware('role.check:admin');

        Route::delete('garage/{id}', [GarageController::class, 'destroy'])
            ->middleware('role.check:admin');
    });
