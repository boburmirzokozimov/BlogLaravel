<?php

namespace App\Domain\User\ValueObjects;

class Email
{
    public function __construct(
        public string $email
    )
    {
    }

    public function value(): string
    {
        return $this->email;
    }
}
