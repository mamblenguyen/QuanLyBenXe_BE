<?php

use App\Http\Controllers\System\TicketController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('/tickets')
    // ->middleware(JwtMiddleware::class)
    ->group(function () {
        Route::post('/book', [TicketController::class, 'store'])
            ->middleware('role.check:admin');

        Route::get('/booked-seats/{route_id}', [TicketController::class, 'getBookedSeats'])
            ->middleware('role.check:admin');

        Route::get('routes/{route_id}/seats', [TicketController::class, 'checkSeats'])
            ->middleware('role.check:admin');

        Route::get('routes/{route_id}/seat-availability', [TicketController::class, 'checkSeatAvailability'])
            ->middleware('role.check:admin');;

    });
