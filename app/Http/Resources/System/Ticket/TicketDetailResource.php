<?php

// app/Http/Resources/TicketDetailResource.php
namespace App\Http\Resources\System\Ticket;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketDetailResource extends JsonResource
{
    public function toArray( $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'phone' => $this->phone_number,
            'depart_station' => $this->schedule->route->departureStation->name,
            'depart_adress' => $this->schedule->route->departureStation->address,
            'arrival_station' => $this->schedule->route->arrivalStation->name,
            'arrival_adress' => $this->schedule->route->arrivalStation->address,
            'depart_time' =>$this->schedule->departure_time,
            'arrival_time' =>$this->schedule->arrival_time,
            'ticket_price' => $this->schedule->ticket_price,
            'date' =>  $this->date,
            'bus_model' => $this->schedule->route->bus->busModel->name,
            'bus_number' => $this->schedule->route->bus->bus_number,
            'ticket_seat' => $this->seats->map(function ($seat) {
                return $seat->seat_number;
            }),
        ];
    }

}
