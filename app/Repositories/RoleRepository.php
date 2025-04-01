<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Implementations\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
    public function attachRolesToUser(string $userId, array $roleIds)
    {
        $roleUserData = [];
        foreach ($roleIds as $roleId) {
            $roleUserData[] = [
                'id' => (string) Str::uuid(), // Generate a UUID for the role_user entry
                'user_id' => $userId,
                'role_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('role_users')->insert($roleUserData); // Insert using the DB facade
    }
    public function getRoleByName($name)
    {
        return $this->model->where('name', $name)->first();
    }
}
