<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetCompleteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->setAddress($this->user->email, $this->user->first_name . ' ' . $this->user->last_name);
        $this->subject('Password Updated');
        return $this->markdown('emails.default')->with([
            'user' => $this->user,
            'message' => 'Your password was reset, hope you were the one that did that!'
        ]);
    }
}
