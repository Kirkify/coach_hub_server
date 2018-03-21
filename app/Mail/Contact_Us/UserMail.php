<?php

namespace App\Mail\Contact_Us;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ContactRequest $user)
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
        $this->subject('Contact Request Received');
        return $this->markdown('emails.contact_us.user');
    }
}
