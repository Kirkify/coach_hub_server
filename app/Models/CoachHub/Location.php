<?php

namespace App\Models\CoachHub;

use App\Models\CoachHub\Coach\CoachBaseProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
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
        return $this->where('id', $value)->first() ?? abort(404, trans('crud.not_found'));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'street_number',
        'street_name',
        'apt_number',
        'city',
        'province',
        'postal_code',
        'notes'
    ];

    /**
     * Get the coach that owns the location.
     */
    public function coach()
    {
        return $this->belongsTo(CoachBaseProfile::class);
    }

    public function programs() {
        return $this->hasMany(Program::class);
    }
}
