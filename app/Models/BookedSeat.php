<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookedSeat extends Model
{
    protected $fillable = ['route_id', 'seat_number'];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
