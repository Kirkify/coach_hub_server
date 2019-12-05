<?php

namespace App\Http\Resources\FormHub;

use App\Http\Resources\CoachHub\Coach\CoachBaseProfileResource;
use App\Http\Resources\CoachHub\ProgramPriceResource;
use App\Http\Resources\IdResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
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
            'value' => $this->value,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at
        ];
    }
}