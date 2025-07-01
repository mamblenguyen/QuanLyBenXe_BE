<?php

namespace App\Repositories\Implementations;

interface BusRepositoryInterface extends BaseRepositoryInterface
{
    public function getBusByBusNumber($busNumber);

    public function allWithLatestLocation();

    public function getBusCountByStatus($id);

}
