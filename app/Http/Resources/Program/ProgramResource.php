<?php

namespace App\Http\Resources\Program;

use App\Http\Resources\IdResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
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
            'program_title' => $this->program_title,
            'program_description' => $this->program_description,
            'category' => $this->category ?? '',
            'registration_start' => $this->registration_start,
            'registration_end' => $this->registration_end,
            'program_start' => $this->program_start,
            'program_end' => $this->program_end,
            'max_participants' => $this->max_participants,
            'has_wait_list' => $this->has_wait_list,
            'location_id' => $this->location_id,
            'tags' => IdResource::collection($this->tags),
            'registrations_count' => $this->registrations_count ?? 0
        ];
    }
}