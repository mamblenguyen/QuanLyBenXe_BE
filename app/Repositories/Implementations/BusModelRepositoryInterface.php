<?php

namespace App\Repositories\Implementations;

interface BusModelRepositoryInterface extends BaseRepositoryInterface
{
    public function getBusModelByBusId($id);
    public function getAllOutGarage();
}
