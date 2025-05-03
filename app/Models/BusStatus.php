<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function buses()
    {
        return $this->hasMany(Bus::class, 'status_id');
    }
}
