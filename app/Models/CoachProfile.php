<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function user()
    {
        return $this->belongsTo(User::class);
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
