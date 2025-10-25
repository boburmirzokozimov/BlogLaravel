<?php

namespace App\Domain\User\ValueObjects;

class PasswordHash
{
    public function __construct(
        public string $password
    )
    {
    }

    public static function fromPlain(string $plain): PasswordHash
    {
        return new self(
            bcrypt($plain)
        );
    }

    public function value()
    {
        return $this->password;
    }
}
