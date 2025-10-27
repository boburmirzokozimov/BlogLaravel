<?php

declare(strict_types=1);

namespace App\Domain\Blog\ValueObjects;

use App\Shared\Exceptions\InvariantViolation;
use Stringable;

final readonly class Content implements Stringable
{
    private function __construct(
        private string $value
    ) {
    }

    /**
     * Create content with validation
     */
    public static function fromString(string $content): self
    {
        $trimmed = trim($content);

        if (strlen($trimmed) < 10) {
            throw new InvariantViolation('Content must be at least 10 characters long');
        }

        if (strlen($trimmed) > 50000) {
            throw new InvariantViolation('Content must not exceed 50000 characters');
        }

        return new self($trimmed);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function excerpt(int $length = 200): string
    {
        if (strlen($this->value) <= $length) {
            return $this->value;
        }

        return substr($this->value, 0, $length) . '...';
    }

    public function wordCount(): int
    {
        return str_word_count($this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
