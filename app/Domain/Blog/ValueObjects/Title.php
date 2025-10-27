<?php

namespace App\Domain\Blog\ValueObjects;

use App\Shared\Exceptions\InvariantViolation;

class Title
{
    private function __construct(
        private string $title
    )
    {
    }

    public static function new(string $title): Title
    {
        if (strlen($title) < 3) {
            throw new InvariantViolation("Title must be at least 3 characters long");
        }

        return new self($title);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function __toString(): string
    {
        return $this->title;
    }

}
