<?php

namespace App\Http\Resources\CoachHub;

use App\Http\Resources\IdResource;
use App\Http\Resources\PartialUser\PartialUser;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramPriceResource extends JsonResource
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
            'guid' => $this->guid,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'price' => $this->price,
            'has_wait_list' => $this->has_wait_list,
            'sub_options' => $this->sub_options,
            'sub_options_preset' => $this->sub_options_preset,
            'multi_sub_options_required' => $this->multi_sub_options_required
        ];
    }
}
