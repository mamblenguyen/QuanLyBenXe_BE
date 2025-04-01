<?php

namespace App\Repositories\Implementations;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserByEmail($email);
}
