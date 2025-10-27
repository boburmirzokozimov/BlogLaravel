<?php

declare(strict_types=1);

namespace App\Domain\Blog\ValueObjects;

use App\Shared\ValueObjects\Id;
use Stringable;

final readonly class AuthorId implements Stringable
{
    private function __construct(
        private Id $value
    ) {
    }

    /**
     * Create from User ID
     */
    public static function fromUserId(Id $userId): self
    {
        return new self($userId);
    }

    /**
     * Create from UUID string
     */
    public static function fromString(string $uuid): self
    {
        return new self(Id::fromString($uuid));
    }

    public function toId(): Id
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value->toString();
    }

    public function equals(self $other): bool
    {
        return $this->value->equals($other->value);
    }

    public function __toString(): string
    {
        return $this->value->toString();
    }
}
