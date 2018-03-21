<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone_number', 'message', 'prefer_call', 'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
