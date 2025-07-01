<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusLocation extends Model
{
    protected $fillable = [
        'bus_id',
        'location',
        'arrived_at',
        'latitude',
        'longitude',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
