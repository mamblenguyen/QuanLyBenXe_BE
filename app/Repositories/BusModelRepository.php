<?php

namespace App\Repositories;

use App\Models\Bus;
use App\Models\BusModel;
use App\Repositories\Implementations\BusModelRepositoryInterface;

class BusModelRepository extends BaseRepository implements BusModelRepositoryInterface
{
    public function __construct(BusModel $model)
    {
        parent::__construct($model);
    }

    public function getBusModelByBusId($id)
    {
        return $this->model->where('id', $id)->first();
    }

}
