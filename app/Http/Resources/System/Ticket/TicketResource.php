<?php

namespace App\Http\Resources\System\Ticket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'name' => $this->full_name,
            'phone' => $this->phone_number,
            'route' => $this->schedule->route,
            'schedule' => $this->schedule,
            'date' =>  $this->date,
            'ticket_seat' => $this->seats->map(function ($seat) {
                return
                    $seat->seat_number;
            }),
        ];
    }
}
