<?php

declare(strict_types=1);

namespace App\Domain\Blog\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use Stringable;

final readonly class PublishedAt implements Stringable
{
    private function __construct(
        private DateTimeImmutable $value
    ) {
    }

    /**
     * Create with current timestamp
     */
    public static function now(): self
    {
        return new self(new DateTimeImmutable());
    }

    /**
     * Create from DateTimeInterface
     */
    public static function fromDateTime(DateTimeInterface $dateTime): self
    {
        if ($dateTime instanceof DateTimeImmutable) {
            return new self($dateTime);
        }

        return new self(DateTimeImmutable::createFromInterface($dateTime));
    }

    /**
     * Create from string
     */
    public static function fromString(string $dateTime): self
    {
        return new self(new DateTimeImmutable($dateTime));
    }

    public function toDateTime(): DateTimeImmutable
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value->format('Y-m-d H:i:s');
    }

    public function toIso8601(): string
    {
        return $this->value->format(DateTimeInterface::ATOM);
    }

    public function isBefore(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function isAfter(self $other): bool
    {
        return $this->value > $other->value;
    }

    public function equals(self $other): bool
    {
        return $this->value == $other->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
