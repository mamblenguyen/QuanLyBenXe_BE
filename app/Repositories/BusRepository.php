<?php

namespace App\Repositories;

use App\Models\Bus;
use App\Models\BusModel;
use App\Repositories\Implementations\BusRepositoryInterface;

class BusRepository extends BaseRepository implements BusRepositoryInterface
{
    public function __construct(Bus $model)
    {
        parent::__construct($model);
    }

    public function getBusByBusNumber($busNumber)
    {
        return $this->model->where('bus_number', $busNumber)->first();
    }

}
