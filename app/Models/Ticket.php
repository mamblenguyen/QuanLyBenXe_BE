<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id', 'full_name', 'phone_number', 'email', 'pickup_point',
        'dropoff_point', 'seat_number', 'price_paid', 'date'
    ];

    // Relationship: A ticket belongs to a schedule
    // public function schedule(): BelongsTo
    // {
    //     return $this->belongsTo(Schedule::class);
    // }

    // Relationship: A ticket belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function seats()
    {
        return $this->hasMany(TicketSeat::class);
    }
    public function schedule()
    {
        return $this->belongsTo(RouteSchedule::class );
    }
}
