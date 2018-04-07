<?php

namespace App\Jobs;

use App\Mail\WelcomeMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmEmailMail;

class UserRegisteredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = null;
        if (!$this->user->verified) {
            // If not verified (most cases)
            $email = new ConfirmEmailMail($this->user);
        } else {
            // User could potentially be pre verified on signup if authenticated
            // through third party OAuth
            $email = new WelcomeMail($this->user);
        }

        Mail::to($this->user->email)->send($email);
    }
}
