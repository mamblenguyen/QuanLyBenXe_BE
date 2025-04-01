<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusStation extends Model
{
    use HasFactory;

    // Các trường có thể được gán (mass-assignable)
    protected $fillable = ['name', 'garage_id', 'province_id', 'address'];

    /**
     * Liên kết trạm xe buýt thuộc về một tỉnh.
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    public function garage()
    {
        return $this->belongsTo(Garage::class);
    }
    public function departureRoutes()
    {
        return $this->hasMany(Route::class, 'departure_station_id');
    }

    public function arrivalRoutes()
    {
        return $this->hasMany(Route::class, 'arrival_station_id');
    }
}
