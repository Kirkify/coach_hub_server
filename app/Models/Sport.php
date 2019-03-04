<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
