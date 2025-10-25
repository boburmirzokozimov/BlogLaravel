<?php
declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

final class Email implements Stringable, JsonSerializable
{
    private string $value;

    private function __construct(string $value)
    {
        $norm = mb_strtolower(trim($value));
        if (!filter_var($norm, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email: {$value}");
        }
        $this->value = $norm;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
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
}
