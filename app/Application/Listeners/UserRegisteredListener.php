<?php

namespace App\Application\Listeners;

use App\Application\Events\UserRegisterEvent;
use App\Application\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;

class UserRegisteredListener
{
    public function handle(UserRegisterEvent $event): void
    {
        // Send verification email for email registrations
        Mail::to($event->user->email()->value())
            ->send(new EmailVerificationMail($event->user, $event->verificationToken));
    }
}
