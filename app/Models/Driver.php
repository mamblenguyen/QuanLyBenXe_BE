<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email' , 'role' , 'garage_id'
    ];
    public function Garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class , 'garage_id');
    }
}
