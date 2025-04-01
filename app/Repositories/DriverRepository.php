<?php

namespace App\Repositories;

use App\Models\Driver;
use App\Models\Role;
use App\Repositories\Implementations\DriverRepositoryInterface;
use App\Repositories\Implementations\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DriverRepository extends BaseRepository implements DriverRepositoryInterface
{
    public function __construct(Driver $model)
    {
        parent::__construct($model);
    }
    public function getDriverByName($name)
    {
        return $this->model->where('name', $name)->first();
    }
}
