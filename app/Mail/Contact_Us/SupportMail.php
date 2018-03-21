<?php

namespace App\Mail\Contact_Us;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SupportMail extends Mailable
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
        $this->subject('Contact Request');
        $this->setAddress(config('mail.support_email'));
        $this->replyTo($this->user->email, $this->user->first_name . ' ' . $this->user->last_name);
        return $this->markdown('emails.contact_us.support');
    }
}
