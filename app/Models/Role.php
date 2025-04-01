<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name'];

    /**
     * Quan hệ một-nhiều với RoleUser.
     * Một role có thể có nhiều người dùng.
     */
    public function roleUsers(): HasMany
    {
        return $this->hasMany(RoleUser::class, 'role_id');
    }

    /**
     * Lấy tất cả người dùng có role này.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_users', 'role_id', 'user_id');
    }

    /**
     * Tìm role theo tên.
     */
    public static function findByName($name)
    {
        return self::where('name', $name)->first();
    }
}
