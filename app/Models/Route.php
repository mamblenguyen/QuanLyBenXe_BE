<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'departure_station_id', 'arrival_station_id', 'garage_id',
        'bus_id', 'driver_id', 'route_status_id'
    ];

    public function departureStation()
    {
        return $this->belongsTo(BusStation::class, 'departure_station_id');
    }

    public function arrivalStation()
    {
        return $this->belongsTo(BusStation::class, 'arrival_station_id');
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function garage()
    {
        return $this->belongsTo(Garage::class, 'garage_id');
    }

    public function status()
    {
        return $this->belongsTo(RouteStatus::class, 'route_status_id');
    }

    // New relationship with route_schedules
    public function schedules()
    {
        return $this->hasMany(RouteSchedule::class);
    }
}
