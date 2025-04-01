<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\System\ProvincesController;


Route::prefix('/province')->name('provinces.')
    ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::get('/list', [ProvincesController::class, 'getAll'])
            ->middleware('role.check:admin');
    });
