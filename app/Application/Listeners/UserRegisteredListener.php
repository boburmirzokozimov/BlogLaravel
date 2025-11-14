<?php

namespace App\Application\Listeners;

use App\Application\Events\UserRegisterEvent;
use App\Application\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class UserRegisteredListener
{
    public function __construct()
    {
    }

    public function handle(UserRegisterEvent $event): void
    {
        Mail::to($event->user->email()->value())->send(new WelcomeMail($event->user));
    }
}
