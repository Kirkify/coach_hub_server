<?php

namespace App\Http\Resources\CoachProfile;

use App\Http\Resources\IdResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CoachProfileResource extends JsonResource
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
            'sports' => IdResource::collection($this->sports),
            'coaching_experience' => $this->coaching_experience,
            'athletic_highlights' => $this->athletic_highlights,
            'session_plan' => $this->session_plan,
            'one_sentence_bio' => $this->one_sentence_bio
        ];
    }
}
