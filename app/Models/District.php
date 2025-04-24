<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'province_id', 'gso_id', 'created_at', 'updated_at'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public static function getDistrictByProvinceId($id)
    {
        return self::where('province_id', $id)->get(); // ✅ trả về Collection để dùng isEmpty()
    }
}
