<?php

use App\Http\Controllers\System\TicketController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('/tickets')
    // ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::post('/book', [TicketController::class, 'store'])
            ->middleware('role.check:admin');

        Route::get('/list', [TicketController::class, 'list'])
            ->middleware('role.check:admin');

        Route::get('/detail/{id}', [TicketController::class, 'detail'])
            ->middleware('role.check:admin');

        Route::get('/booked-seats/{schedule_id}', [TicketController::class, 'getBookedSeats'])
            ->middleware('role.check:admin');

        Route::get('routes/{schedule_id}/seats', [TicketController::class, 'checkSeats'])
            ->middleware('role.check:admin');

        Route::get('routes/{schedule_id}/seat-availability/{date}', [TicketController::class, 'checkSeatAvailability'])
            ->middleware('role.check:admin');;

    });
