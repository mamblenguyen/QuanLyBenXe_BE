<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\System\RoleController;

Route::prefix('/role')->name('role.')
    ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::get('/list', [RoleController::class, 'getAll'])
            ->middleware('role.check:admin');

        Route::get('roles', [RoleController::class, 'index'])
            ->middleware('role.check:staff,admin');

        Route::get('role/{id}', [RoleController::class, 'show'])
            ->middleware('role.check:admin,traffic');

        Route::post('role', [RoleController::class, 'store'])
            ->middleware('role.check:admin');

        Route::put('role/{id}', [RoleController::class, 'update'])
            ->middleware('role.check:admin');

        Route::delete('role/{id}', [RoleController::class, 'destroy'])
            ->middleware('role.check:admin');
    });
