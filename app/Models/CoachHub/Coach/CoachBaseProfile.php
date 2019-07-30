<?php

namespace App\Models\CoachHub\Coach;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CoachHub\Program;
use App\Models\CoachHub\Location;
use App\Models\CoachHub\Registration;
use App\Models\CoachHub\Tag;

class CoachBaseProfile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username',
        'gender', 'date_of_birth'
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that owns the profile.
     */
    public function coachProfiles()
    {
        return $this->hasMany(CoachProfile::class);
    }

    public function programs() {
        return $this->hasMany(Program::class);
    }

    public function locations() {
        return $this->hasMany(Location::class);
    }

    public function registrations() {
        return $this->hasMany(Registration::class);
    }

    public function tags() {
        return $this->hasMany(Tag::class);
    }
}
