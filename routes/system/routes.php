<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\System\RouteController;


Route::prefix('/route')->name('route.')
    ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::get('/list', [RouteController::class, 'getAll'])
            ->middleware('role.check:admin');

        Route::post('/create', [RouteController::class, 'create'])
            ->middleware('role.check:admin');

        Route::get('/detail/{id}', [RouteController::class, 'show'])
            ->middleware('role.check:admin');

        Route::put('/update/{id}', [RouteController::class, 'update'])
            ->middleware('role.check:admin');

        Route::delete('/delete/{id}', [RouteController::class, 'destroy'])
            ->middleware('role.check:admin');
    });
