<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
// use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Traits\Messagable;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Messagable, Notifiable;

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
        'password', 'roles'
    ];

    /**
     * Route notifications for the Nexmo channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForNexmo($notification)
    {
        return $this->profile->phone_number;
    }

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

    public function relationships() {
        return $this->hasMany(Relationship::class);
    }

    public function profile() {
        return $this->hasOne(UserProfile::class);
    }

    public function confirmEmail() {
        return $this->hasOne(ConfirmEmail::class);
    }

    public function hasFriend($userId) {
        // TODO: Implement friend list table
        return true;
    }
}
