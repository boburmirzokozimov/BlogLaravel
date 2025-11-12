<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use Illuminate\Support\Carbon;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

final class Email implements Stringable, JsonSerializable
{
    private string $value;

    private ?Carbon $emailVerifiedAt;

    private function __construct(string $value, ?Carbon $emailVerifiedAt = null)
    {
        $norm = mb_strtolower(trim($value));
        if (!filter_var($norm, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email: {$value}");
        }
        $this->value = $norm;
        $this->emailVerifiedAt = $emailVerifiedAt;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function reconstitute(string $email, ?Carbon $verifiedAt): self
    {
        return new self($email, $verifiedAt);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function activate(): void
    {
        $this->emailVerifiedAt = Carbon::now();
    }

    public function active(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function change(string $email): void
    {
        $this->value = $email;
    }

    public function getEmailVerifiedAt(): ?Carbon
    {
        return $this->emailVerifiedAt;
    }

    public function isEqual(self $other): bool
    {
        return $this->value === $other->value;
    }
}
