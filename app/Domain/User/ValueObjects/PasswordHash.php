<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Stringable;

final class PasswordHash implements Stringable
{
    private string $hash;

    private function __construct(string $hash)
    {
        if (strlen($hash) < 20) { // bcrypt hashes are ~60 chars
            throw new InvalidArgumentException('Invalid password hash.');
        }
        $this->hash = $hash;
    }

    /** Factory: from already hashed value (e.g. from DB) */
    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    public static function generateRandom(): self
    {
        return self::fromPlain(Str::random(6));
    }

    /** Factory: from plain password (hash internally) */
    public static function fromPlain(string $plain): self
    {
        if (strlen($plain) < 6) {
            throw new InvalidArgumentException('Password too short.');
        }

        return new self(password_hash($plain, PASSWORD_BCRYPT));
    }

    /** Verify a plaintext password against this hash */
    public function verify(string $plain): bool
    {
        return password_verify($plain, $this->hash);
    }

    public function value(): string
    {
        return $this->hash;
    }

    public function __toString(): string
    {
        return $this->hash;
    }

    public function equals(self $other): bool
    {
        return hash_equals($this->hash, $other->hash);
    }
}
