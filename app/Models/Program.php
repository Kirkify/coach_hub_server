<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

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
        'program_title', 'program_description',
        'registration_start', 'registration_end',
        'program_start', 'program_end', 'location_id'
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the phone record associated with the user.
     */
    public function location()
    {
        return $this->hasOne(Location::class);
    }

    /**
     * Get all of the tags for the program.
     */
    public function tags()
    {
        return $this->morphToMany(
            'App\Tag',
            'taggable'
        );
    }
}
