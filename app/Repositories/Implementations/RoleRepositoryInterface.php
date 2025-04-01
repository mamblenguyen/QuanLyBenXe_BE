<?php

namespace App\Repositories\Implementations;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function getRoleByName($name);
}
