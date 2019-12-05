<?php

namespace App\Models\FormHub;

use App\Models\Traits\UuidTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
//    use UuidTrait;

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'value',
    ];

    public function setFormAttribute($form)
    {
        if ($form !== null) {
            $this->attributes['value'] = json_encode($form);
        }
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function isUserOwner(User $user)
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
}
