<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\System\RouteController;


Route::prefix('/route')->name('route.')
    ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::get('/list', [RouteController::class, 'getAll'])
            ->middleware('role.check:admin');

        Route::get('/list/action', [RouteController::class, 'getAllAction'])
            ->middleware('role.check:admin');

        Route::post('/create', [RouteController::class, 'create'])
            ->middleware('role.check:admin');
        Route::post('/createSchedule/{id}', [RouteController::class, 'createSchedule'])
            ->middleware('role.check:admin');
        Route::get('/detail/{id}', [RouteController::class, 'show'])
            ->middleware('role.check:admin');
        Route::get('/detail/{id}/status', [RouteController::class, 'showStatus'])
            ->middleware('role.check:admin');
        Route::get('/detail/schedule/{scheduleId}', [RouteController::class, 'showScheduleDetail'])
            ->middleware('role.check:admin');

        Route::put('/update/{id}', [RouteController::class, 'update'])
            ->middleware('role.check:admin');
        Route::put('/update/schedule/{id}', [RouteController::class, 'updateSchedule'])
            ->middleware('role.check:admin');
        Route::delete('/delete/{id}', [RouteController::class, 'destroy'])
            ->middleware('role.check:admin');
        Route::put('/route/{id}/soft-delete', [RouteController::class, 'softDelete'])
            ->middleware('role.check:admin');
        Route::put('/route/{id}/restore', [RouteController::class, 'restore'])
            ->middleware('role.check:admin');

        Route::put('/route/{id}/soft-delete-schedule', [RouteController::class, 'softDeleteSchedule'])
            ->middleware('role.check:admin');
        Route::put('/route/{id}/restore-schedule', [RouteController::class, 'restoreSchedule'])
            ->middleware('role.check:admin');
        Route::get('/statistic', [RouteController::class, 'getRouteAndScheduleStats'])
            ->middleware('role.check:admin');
    });
