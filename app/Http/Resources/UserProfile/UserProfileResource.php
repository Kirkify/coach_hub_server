<?php

namespace App\Http\Resources\UserProfile;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'profile_pic_url' => $this->profile_pic_url,
            'phone_number' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'street_number' => $this->street_number,
            'street_name' => $this->street_name,
            'apt_number' => $this->apt_number,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code
        ];
    }
}
