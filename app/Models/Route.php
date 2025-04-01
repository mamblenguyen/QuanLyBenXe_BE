<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    // Các trường có thể gán (mass-assignable)
    protected $fillable = [
        'departure_station_id', 'arrival_station_id', 'garage_id',
        'departure_time', 'arrival_time', 'ticket_price', 'bus_id', 'driver_id'
    ];

    /**
     * Liên kết với trạm xe buýt khởi hành (departure station).
     */
    public function departureStation()
    {
        return $this->belongsTo(BusStation::class, 'departure_station_id');
    }

    /**
     * Liên kết với trạm xe buýt đến (arrival station).
     */
    public function arrivalStation()
    {
        return $this->belongsTo(BusStation::class, 'arrival_station_id');
    }

    /**
     * Liên kết với xe buýt (bus).
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    /**
     * Liên kết với tài xế (driver).
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function garage()
    {
        return $this->belongsTo(Garage::class, 'garage_id');
    }
}
