<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'garage_id',
        'province_id',
        'status_id',
        'address',
    ];

    // Trạm xe thuộc tỉnh nào
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    // Trạm xe thuộc garage nào
    public function garage()
    {
        return $this->belongsTo(Garage::class);
    }

    // Trạm là điểm khởi hành của tuyến
    public function departureRoutes()
    {
        return $this->hasMany(Route::class, 'departure_station_id');
    }

    // Trạm là điểm đến của tuyến
    public function arrivalRoutes()
    {
        return $this->hasMany(Route::class, 'arrival_station_id');
    }

    // Trạng thái của trạm
    public function status()
    {
        return $this->belongsTo(StationStatus::class, 'status_id');
    }
}
