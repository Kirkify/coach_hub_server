<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfirmEmail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'verified', 'new_email'
    ];

    public function generateToken() {

    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
