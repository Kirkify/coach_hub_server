<?php

namespace App\Models\CoachHub;

use Illuminate\Database\Eloquent\Model;
use App\Models\CoachHub\Coach\CoachProfile;

class Sport extends Model
{
    /**
     * Get all of the coach profiles that are assigned this sport.
     */
    public function coachProfiles()
    {
        return $this->morphedByMany(CoachProfile::class, 'sportable');
    }
}
