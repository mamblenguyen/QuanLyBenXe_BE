<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'district_id', 'gso_id', 'created_at', 'updated_at'];

    public function District()
    {
        return $this->belongsTo(District::class);
    }

    public static function getWardByDistrictId($id)
    {
        return self::where('district_id', $id)->get(); // ✅ trả về Collection để dùng isEmpty()
    }
}
