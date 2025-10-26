<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

final class Status implements Stringable, JsonSerializable
{
    private const ACTIVE = 'active';

    private const INACTIVE = 'inactive';

    private const SUSPENDED = 'suspended';

    private const PENDING = 'pending';

    private string $value;

    private function __construct(string $value)
    {
        if (!in_array($value, self::allowed(), true)) {
            throw new InvalidArgumentException("Invalid user status: {$value}");
        }

        $this->value = $value;
    }

    /**
     * @return string[]
     */
    public static function allowed(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
            self::SUSPENDED,
            self::PENDING,
        ];
    }

    public static function inactive(): self
    {
        return new self(self::INACTIVE);
    }

    public static function suspended(): self
    {
        return new self(self::SUSPENDED);
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /** Named constructors for clarity */
    public static function active(): self
    {
        return new self(self::ACTIVE);
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
        $this->value = self::ACTIVE;
    }
}
