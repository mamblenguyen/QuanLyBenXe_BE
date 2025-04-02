<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject{
    use HasFactory, Notifiable;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'id', 'name', 'email', 'garage_id', 'password', 'phone', 'address', 'avatar',
        'identity_card', 'identity_issue_place', 'identity_issue_date', 'status', 'birthday', 'gender',
        'license', 'license_issue_date', 'license_expiry_date'
    ];

    protected $dates = [
        'birthday', 'identity_issue_date', 'license_issue_date', 'license_expiry_date'
    ];

    /**
     * Quan hệ nhiều-nhiều với Role thông qua RoleUser.
     * Một người dùng có thể có nhiều role.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }

    public function Garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class, 'garage_id'); // Foreign key: model_id
    }
    public function buses(): HasMany
    {
        return $this->hasMany(BusModel::class);
    }


    /**
     * Tìm user theo email.
     */
    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * Lấy danh sách user có tên cụ thể.
     */
    public static function findByName($name)
    {
        return self::where('name', 'like', "%$name%")->get();
    }

    /**
     * Xác thực email và password.
     */
    public static function authenticate($email, $password)
    {
        $user = self::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }
        return null;
    }
    public function hasRole($role)
    {
        return $this->role === $role;
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
