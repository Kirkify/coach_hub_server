<?php

namespace App\Models\CoachHub;

use App\Models\CoachHub\Coach\CoachBaseProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramPrice extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'price', 'guid', 'category',
        'has_wait_list', 'capacity',
        'sub_options', 'sub_options_preset',
        'multi_sub_options_required'
    ];

    public function setSubOptionsAttribute($options)
    {
        if ($options !== null) {
            $this->attributes['sub_options'] = json_encode($options);
        }
    }

    /**
     * Get the coach that owns the profile.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
