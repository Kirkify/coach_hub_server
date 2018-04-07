<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
// use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, /*Messagable,*/ Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, $this->getEmailForPasswordReset()));
    }

    public function socialAccounts() {
        return $this->hasMany(SocialAccount::class);
    }

    public function contactRequests() {
        return $this->hasMany(ContactRequest::class);
    }

    public function profile() {
        return $this->hasOne(UserProfile::class);
    }

    public function confirmEmail() {
        return $this->hasOne(ConfirmEmail::class);
    }
}
