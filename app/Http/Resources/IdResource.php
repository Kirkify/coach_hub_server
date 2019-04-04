<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IdResource extends JsonResource
{
    /**
     * Transform the resource into just the id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return integer
     */
    public function toArray($request)
    {
        return $this->id;
    }
}
