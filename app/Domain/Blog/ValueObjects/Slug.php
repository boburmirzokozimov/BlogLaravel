<?php

declare(strict_types=1);

namespace App\Domain\Blog\ValueObjects;

use Stringable;

final readonly class Slug implements Stringable
{
    private function __construct(
        private string $value
    ) {
    }

    /**
     * Create slug from title (auto-generate).
     */
    public static function fromTitle(string $title): self
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        return new self($slug);
    }

    /**
     * Create from existing slug string.
     */
    public static function fromString(string $slug): self
    {
        return new self($slug);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
