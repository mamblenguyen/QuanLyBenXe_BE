<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Một trạng thái có thể thuộc nhiều trạm xe
    public function busStations()
    {
        return $this->hasMany(BusStation::class, 'status_id');
    }
}
