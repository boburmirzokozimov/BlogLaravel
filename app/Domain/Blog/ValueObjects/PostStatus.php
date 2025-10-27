<?php

namespace App\Domain\Blog\ValueObjects;

use InvalidArgumentException;

class PostStatus
{
    private const DRAFT = 'draft';
    private const PUBLISHED = 'published';
    private const ARCHIVED = 'archived';

    private function __construct(private readonly string $value)
    {
        if (!in_array($value, [self::DRAFT, self::PUBLISHED, self::ARCHIVED], true)) {
            throw new InvalidArgumentException("Invalid status: {$value}");
        }
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function published(): self
    {
        return new self(self::PUBLISHED);
    }

    public static function archived(): self
    {
        return new self(self::ARCHIVED);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
