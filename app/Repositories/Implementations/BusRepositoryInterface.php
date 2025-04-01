<?php

namespace App\Repositories\Implementations;

interface BusRepositoryInterface extends BaseRepositoryInterface
{
    public function getBusByBusNumber($busNumber);
}
