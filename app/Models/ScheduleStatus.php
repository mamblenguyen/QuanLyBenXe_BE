<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    // Relationship with the route
    public function schedules()
    {
        return $this->hasMany(RouteSchedule::class, 'schedule_status_id');
    }
}
