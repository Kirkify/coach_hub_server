<?php

namespace App\Models\CoachHub;

use App\Models\CoachHub\Coach\CoachBaseProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['tags'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $withCount = ['registrations'];

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        return $this->where('id', $value)->first() ?? abort(404, trans('programs.not_found'));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'program_title', 'program_description', 'category',
        'registration_start', 'registration_end', 'has_wait_list',
        'program_start', 'program_end', 'max_participants', 'location_id'
    ];

    /**
     * Get the coach that owns the profile.
     */
    public function coach()
    {
        return $this->belongsTo(CoachBaseProfile::class);
    }

    /**
     * Get the phone record associated with the user.
     */
    public function location()
    {
        return $this->hasOne(Location::class);
    }

    /**
     * Get the phone record associated with the user.
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Get all of the tags for the program.
     */
    public function tags()
    {
        return $this->morphToMany(
            Tag::class,
            'taggable'
        );
    }
}
