<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'departure_time',
        'arrival_time',
        'ticket_price',
        'schedule_status_id'
    ];

    // Relationship with the route
   public function route()
{
    return $this->belongsTo(Route::class, 'route_id');
}


    public function status()
    {
        return $this->belongsTo(ScheduleStatus::class, 'schedule_status_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'schedule_id');
    }
}
