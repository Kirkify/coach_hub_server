<?php

namespace App\Http\Resources\Program;

use App\Http\Resources\CoachHub\Coach\CoachBaseProfileResource;
use App\Http\Resources\CoachHub\ProgramPriceResource;
use App\Http\Resources\FormHub\FormResource;
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
            'program_title' => $this->program_title ?? '',
            'program_description' => $this->program_description ?? '',
            'category' => $this->category ?? '',
            'registration_start' => (string) $this->registration_start,
            'registration_end' => (string) $this->registration_end,
            'program_start' => (string) $this->program_start,
            'program_end' => (string) $this->program_end,
            'location_id' => $this->location_id,
            'prices' => ProgramPriceResource::collection($this->prices),
            'tags' => IdResource::collection($this->tags),
            'coach' => new CoachBaseProfileResource($this->coach),
            'form' => new FormResource($this->form),
            'registrations_count' => $this->registrations_count ?? 0
        ];
    }
}