<?php

namespace App\Listeners;

use App\Jobs\UserRegisteredJob;
use App\Mail\PasswordResetCompleteMail;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\ConfirmEmailMail;
use Illuminate\Support\Facades\Mail;

class PasswordResetListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $email = new PasswordResetCompleteMail($event->user);
        Mail::send($email);
    }
}
