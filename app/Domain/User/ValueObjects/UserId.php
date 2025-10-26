<?php

namespace App\Domain\User\ValueObjects;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Stringable;

final readonly class UserId implements Stringable
{
    private function __construct(
        private string $value
    ) {
        if (!Uuid::isValid($value)) {
            throw new InvalidArgumentException("Invalid UUID: {$value}");
        }
    }

    /**
     * Generate a new random UUID.
     */
    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * Create from existing UUID string.
     */
    public static function fromString(string $uuid): self
    {
        return new self($uuid);
    }

    /**
     * Get the UUID as string.
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Magic method to convert to string.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Check equality with another UserId.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
