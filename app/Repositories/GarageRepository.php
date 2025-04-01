<?php

namespace App\Repositories;

use App\Models\Garage;
use App\Repositories\Implementations\GarageRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GarageRepository extends BaseRepository implements GarageRepositoryInterface
{
    public function __construct(Garage $model)
    {
        parent::__construct($model);
    }

    public function getGarageByName($name)
    {
        return $this->model->where('name', $name)->first();
    }
}
