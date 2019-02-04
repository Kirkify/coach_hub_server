<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'thread_id' => $this->thread_id,
            'user_id' => $this->user_id,
            'body' => $this->body
        ];
    }
}
