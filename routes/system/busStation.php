<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\System\BustationController;

// Route Check with role Traffic
Route::prefix('/bus-station')->name('bus-station.')
    ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::get('/list', [BustationController::class, 'getAll'])
            ->middleware('role.check:admin');

            Route::get('/list/status', [BustationController::class, 'getAllStatus'])
            ->middleware('role.check:admin');
        Route::post('/create', [BustationController::class, 'create'])
            ->middleware('role.check:admin');

        Route::get('/detail/{id}', [BustationController::class, 'show'])
            ->middleware('role.check:admin');

        Route::put('/update/{id}', [BustationController::class, 'update'])
            ->middleware('role.check:admin');

        Route::delete('/delete/{id}', [BustationController::class, 'destroy'])
            ->middleware('role.check:admin');
        Route::put('/bus-stations/{id}/soft-delete', [BustationController::class, 'softDelete'])
            ->middleware('role.check:admin');
        Route::put('/bus-stations/{id}/restore', [BustationController::class, 'restore'])
            ->middleware('role.check:admin');
    });
