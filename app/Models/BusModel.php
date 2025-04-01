<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
    ];

    // A BusModel has many buses
    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class, 'model_id'); // Foreign key: model_id
    }
}
