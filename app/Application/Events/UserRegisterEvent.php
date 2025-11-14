<?php

namespace App\Application\Events;

use App\Domain\User\Entities\User;
use Illuminate\Foundation\Events\Dispatchable;

class UserRegisterEvent
{
    use Dispatchable;

    public function __construct(public User $user)
    {
    }
}
