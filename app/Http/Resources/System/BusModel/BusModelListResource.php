<?php

namespace App\Http\Resources\System\BusModel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusModelListResource extends JsonResource
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
            'name' => $this->name,
            'capacity' => $this->capacity,
        ];
    }
}
