<?php

namespace App\Http\Resources\CoachHub\Registration;

use App\Http\Resources\IdResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth,
            'notes' => $this->notes,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'program_id' => $this->program_id
        ];
    }
}