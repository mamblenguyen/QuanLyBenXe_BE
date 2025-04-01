<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'route_id', 'bus_id', 'departure_time', 'arrival_time', 'available_seats', 'ticket_price'
    ];

    // Relationship: A schedule belongs to a route
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    // Relationship: A schedule belongs to a bus
    public function bus(): BelongsTo
    {
        return $this->belongsTo(BusModel::class);
    }

    // Relationship: A schedule has many tickets
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
