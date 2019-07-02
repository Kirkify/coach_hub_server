<?php

namespace App\Models\CoachHub\Coach;

use Illuminate\Database\Eloquent\Model;
use App\Models\CoachHub\Sport;

class CoachProfile extends Model
{
    protected $with = ['sports'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'coaching_experience', 'athletic_highlights',
        'session_plan', 'one_sentence_bio'
    ];

    /**
     * Get the user that owns the profile.
     */
    public function coachBaseProfile()
    {
        return $this->belongsTo(CoachBaseProfile::class);
    }

    /**
     * Get all of the sports for this profile.
     */
    public function sports()
    {
        return $this->morphToMany(
            Sport::class,
            'sportable'
        );
    }
}
