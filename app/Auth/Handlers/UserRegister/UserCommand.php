<?php

namespace App\Auth\Handlers\UserRegister;


class UserCommand
{
    public function __construct(
        public string $email,
        public string $password,
        public string $name,
    )
    {
    }
}
