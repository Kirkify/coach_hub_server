<?php

namespace App\Http\Resources\CoachHub\Location;

use App\Http\Resources\IdResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'street_number' => $this->street_number,
            'street_name' => $this->street_name,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'notes' => $this->notes,
        ];
    }
}