<?php

namespace App\Http\Resources\CoachHub\Coach;

use Illuminate\Http\Resources\Json\JsonResource;

class CoachBaseProfileResource extends JsonResource
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
            'user_id' => $this->user_id,
            'username' => $this->username,
            'name' => $this->name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'created_at' => (string) $this->created_at
        ];
    }
}
