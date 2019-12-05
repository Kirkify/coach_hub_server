<?php

namespace App\Models\CoachHub;

use App\Models\CoachHub\Coach\CoachBaseProfile;
use App\Models\FormHub\Form;
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
    protected $with = ['tags', 'prices'];

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
        'registration_start', 'registration_end',
        'program_start', 'program_end', 'location_id', 'form_id'
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query)
    {
        return $query->with(['tags', 'prices']);
    }

    /**
     * Get the coach that owns the profile.
     */
    public function coach()
    {
        return $this->belongsTo(CoachBaseProfile::class, 'coach_base_profile_id');
    }

    /**
     * Get the phone record associated with the user.
     */
    public function location()
    {
        return $this->hasOne(Location::class);
    }

    /**
     * Get the form associated with the program.
     */
    public function form()
    {
        return $this->hasOne(Form::class, 'id', 'form_id');
    }

    /**
     * Get the phone record associated with the user.
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Get the phone record associated with the user.
     */
    public function prices()
    {
        return $this->hasMany(ProgramPrice::class);
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
