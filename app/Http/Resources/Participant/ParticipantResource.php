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
        $currentUser = Auth::user()->id === $this->user_id;
        return [
            // Let's hide these attributes for now
//            'created_at' => (string) $this->created_at,
//            'deleted_at' => (string) $this->deleted_at,
//            'updated_at' => (string) $this->updated_at,
            'id' => $this->id,
            'thread_id' => $this->thread_id,
            'user_id' => $this->user_id,
            'is_admin' => $this->is_admin,
            $this->mergeWhen(Auth::user()->id === $this->user_id, [
                'last_read' => (string) $this->last_read,
                'current_user' => true,
            ]),
        ];
    }
}
