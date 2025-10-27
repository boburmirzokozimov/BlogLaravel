<?php

namespace App\Domain\User\Entities;

use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Domain\User\ValueObjects\Status;
use App\Shared\Exceptions\InvariantViolation;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

class User
{
    public function __construct(
        private Id $id,
        private string $name,
        private Email $email,
        private PasswordHash $password,
        private Status $status,
    ) {
    }

    /**
     * Create a new user with generated UUID.
     */
    public static function create(
        string $name,
        Email $email,
        PasswordHash $password
    ): self {
        return new self(
            Id::generate(),
            $name,
            $email,
            $password,
            Status::pending()
        );
    }

    /**
     * Reconstitute user from persistence.
     */
    public static function reconstitute(
        Id $id,
        string $name,
        Email $email,
        PasswordHash $password,
        Status $status
    ): self {
        return new self($id, $name, $email, $password, $status);
    }

    public function id(): Id
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

    public function attachEmail(string $email): void
    {
        if ($this->email->active()) {
            throw new InvariantViolation('email_has_been_activated_already');
        }
        $this->email->change($email);
        $this->email->activate();
    }

    public function activate(): void
    {
        if ($this->status->equals(Status::active())) {
            throw new InvariantViolation('user_has_already_been_activated');
        }
        $this->status->activate();
    }

    public function status(): Status
    {
        return $this->status;
    }
}
