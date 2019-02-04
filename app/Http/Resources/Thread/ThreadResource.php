<?php

namespace App\Http\Resources\Thread;

use App\Http\Resources\Message\MessageResource;
use App\Http\Resources\Participant\ParticipantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
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
            'created_at' => (string) $this->created_at,
            'deleted_at' => (string) $this->deleted_at,
            'updated_at' => (string) $this->updated_at,
            'subject' => $this->subject
        ];
    }
}
