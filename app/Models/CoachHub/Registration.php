<?php

namespace App\Models\CoachHub;

use App\Models\CoachHub\Coach\CoachBaseProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model
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
        'first_name', 'last_name',
        'email', 'date_of_birth',
        'notes', 'program_id'
    ];

    /**
     * Get the coach that owns the registration.
     */
    public function coach()
    {
        return $this->belongsTo(CoachBaseProfile::class);
    }

    /**
     * Get the program associated with the registration.
     */
    public function program()
    {
        return $this->hasOne(Program::class);
    }

    /**
     * Get the program associated with the registration.
     */
    public function programPrices()
    {
        return $this->belongsToMany(ProgramPrice::class)->withTimestamps();
    }
}
