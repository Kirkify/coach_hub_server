<?php

namespace App\Jobs;

use App\Models\ContactRequest;
use App\Mail\Contact_Us\SupportMail;
use App\Mail\Contact_Us\UserMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class ContactRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ContactRequest $user)
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
        $userMail = new UserMail($this->user);
        Mail::send($userMail);

        $supportMail = new SupportMail($this->user);
        Mail::send($supportMail);
    }
}
