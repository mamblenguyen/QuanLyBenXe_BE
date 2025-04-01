<?php

namespace App\Repositories\Implementations;

interface GarageRepositoryInterface extends BaseRepositoryInterface
{
    public function getGarageByName($name);
}
