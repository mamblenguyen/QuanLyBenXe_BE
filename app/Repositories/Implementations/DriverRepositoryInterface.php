<?php

namespace App\Repositories\Implementations;

interface DriverRepositoryInterface extends BaseRepositoryInterface
{
    public function getDriverByName($name);
}
