<?php

namespace App\Repositories;

use App\Models\Bus;
use App\Models\BusModel;
use App\Repositories\Implementations\BusRepositoryInterface;
use Illuminate\Support\Facades\DB;

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
    public function allWithLatestLocation()
    {
        return Bus::with(['locations' => function ($query) {
            $query->latest()->limit(1);
        }])->get();
    }

 public function getBusCountByStatus($garage_id)
{
    return DB::table('bus_statuses')
        ->select('bus_statuses.id as id', 'bus_statuses.name as name', DB::raw('COUNT(buses.id) as total'))
        ->leftJoin('buses', function ($join) use ($garage_id) {
            $join->on('buses.status_id', '=', 'bus_statuses.id')
                 ->where('buses.garage_id', '=', $garage_id);
        })
        ->groupBy('bus_statuses.id', 'bus_statuses.name')
        ->get();
}

}
