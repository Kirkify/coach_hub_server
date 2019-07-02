<?php

namespace App\Models\CoachHub;

use App\Models\CoachHub\Coach\CoachBaseProfile;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * Get the coach that owns the tag.
     */
    public function coach()
    {
        return $this->belongsTo(CoachBaseProfile::class);
    }

    /**
     * Get all of the programs that are assigned to this tag.
     */
    public function programs()
    {
        return $this->morphedByMany(Program::class, 'taggable');
    }
}
