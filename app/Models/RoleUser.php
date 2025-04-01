<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleUser extends Model
{
    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = ['user_id', 'role_id'];

    /**
     * Quan hệ ngược về User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Quan hệ ngược về Role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Tìm tất cả các RoleUser theo user_id.
     */
    public static function findByUserId($userId)
    {
        return self::where('user_id', $userId)->get();
    }

    /**
     * Tìm tất cả các RoleUser theo role_id.
     */
    public static function findByRoleId($roleId)
    {
        return self::where('role_id', $roleId)->get();
    }
}
