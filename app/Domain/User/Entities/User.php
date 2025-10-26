<?php

namespace App\Domain\User\Entities;

use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Domain\User\ValueObjects\UserId;
use App\Shared\Exceptions\InvariantViolation;
use InvalidArgumentException;

class User
{
    public function __construct(
        private UserId       $id,
        private string       $name,
        private Email        $email,
        private PasswordHash $password,
    )
    {
    }

    /**
     * Create a new user with generated UUID
     */
    public static function create(
        string       $name,
        Email        $email,
        PasswordHash $password
    ): self
    {
        return new self(
            UserId::generate(),
            $name,
            $email,
            $password
        );
    }

    /**
     * Reconstitute user from persistence
     */
    public static function reconstitute(
        UserId       $id,
        string       $name,
        Email        $email,
        PasswordHash $password
    ): self
    {
        return new self($id, $name, $email, $password);
    }

    public function id(): UserId
    {
        return $this->id;
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

    public function activateEmail(): void
    {
        if ($this->email->active()) {
            throw new InvariantViolation('email_has_been_activated_already');
        }

        $this->email->activate();
    }
}
