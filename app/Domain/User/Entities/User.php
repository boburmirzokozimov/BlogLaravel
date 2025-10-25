<?php

namespace App\Domain\User\Entities;

use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use InvalidArgumentException;

class User
{
    public function __construct(
        private string       $name,
        private Email        $email,
        private PasswordHash $password,
    )
    {
    }

    public function rename(string $newName): void
    {
        if (strlen($newName) < 3) {
            throw new InvalidArgumentException('User name too short');
        }
        $this->name = $newName;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function password(): PasswordHash
    {
        return $this->password;
    }
}
