<?php

namespace App\Http\Resources\Participant;

use App\Http\Resources\PartialUser\PartialUser;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ParticipantResource extends JsonResource
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
            // Let's hide these attributes for now
//            'created_at' => (string) $this->created_at,
//            'deleted_at' => (string) $this->deleted_at,
//            'updated_at' => (string) $this->updated_at,
            'id' => $this->id,
            'thread_id' => $this->thread_id,
            'user_id' => $this->user_id,
            'is_admin' => $this->is_admin,
            'last_read' => $this->when(Auth::user()->id === $this->user_id, (string) $this->last_read)
        ];
    }
}
