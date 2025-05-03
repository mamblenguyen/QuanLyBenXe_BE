<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\System\BusController;
use App\Http\Controllers\System\BusModelController;

Route::prefix('/bus')->name('bus.')
   ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::get('/list', [BusController::class, 'getAll'])
            ->middleware('role.check:admin');
         Route::get('/listBusModel', [BusModelController::class, 'index'])
            ->middleware('role.check:admin');
        Route::get('buses', [BusController::class, 'index'])
            ->middleware('role.check:staff,admin');

        Route::get('bus/{id}', [BusController::class, 'show'])
            ->middleware('role.check:admin,traffic');

        Route::post('bus', [BusController::class, 'store'])
            ->middleware('role.check:admin');

        Route::post('bus/{id}', [BusController::class, 'update'])
            ->middleware('role.check:admin');

        Route::delete('bus/{id}', [BusController::class, 'destroy'])
            ->middleware('role.check:admin');

        Route::post('/send-sms', [BusController::class, 'sendSms']);
        Route::post('/send-zalo-message', [BusController::class, 'sendZaloMessage']);
        Route::get('/send-active/{key}', [BusController::class, 'SendActiveKey']);
        Route::post('/send-zalo', [BusController::class, 'sendMessage']);
        Route::post('/send-zalo-template-message', [BusController::class, 'sendTemplateMessage']);
        Route::post('/zalo/send-message', [BusController::class, 'sendMessageText']);

    });
