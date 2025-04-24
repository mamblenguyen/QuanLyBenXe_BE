<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\System\ProvincesController;


Route::prefix('/province')->name('provinces.')
    ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::get('/province/list', [ProvincesController::class, 'getAllProvince'])
            ->middleware('role.check:admin');

        Route::get('/district/list/{id}', [ProvincesController::class, 'getAllDistrict'])
            ->middleware('role.check:admin');

        Route::get('/ward/list/{id}', [ProvincesController::class, 'getAllWard'])
            ->middleware('role.check:admin');
    });
