<?php

namespace App\Http\Resources\System\Routes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'departure_station_id' => $this->departureStation,
            'arrival_station_id' => $this->arrivalStation,
            'garage_id' => $this->garage,
            'schedule' => $this->schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'departure_time' => $schedule->departure_time,
                    'arrival_time' => $schedule->arrival_time,
                    'ticket_price' => $schedule->ticket_price,
                    'status_id' => $schedule->status->id ?? null,
                    'status_name' => $schedule->status->name ?? null,
                    'bus_model_id' => $this->bus->model_id ?? null,
                ];
            }),
            'bus_id' => $this->bus,
            'status' => $this->status,
            'driver_id' => $this->driver,
            'created_at' => $this->created_at->toISOString(), // Đảm bảo đây là đối tượng Carbon
            'updated_at' => $this->updated_at->toISOString(), // Đảm bảo đây là đối tượng Carbon
        ];
    }
}
