<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * Get all of the coach profiles that are assigned this sport.
     */
    public function programs()
    {
        return $this->morphedByMany(Program::class, 'taggable');
    }
}
