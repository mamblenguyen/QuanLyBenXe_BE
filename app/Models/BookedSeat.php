<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookedSeat extends Model
{
    protected $fillable = ['schedule_id', 'seat_number'];

    public function schedule()
    {
        return $this->belongsTo(RouteSchedule::class);
    }
}
