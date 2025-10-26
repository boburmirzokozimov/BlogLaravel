<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use Throwable;

final class Conflict extends DomainException
{
    private array $ctx;

    public function __construct(
        string $translationKey = 'errors.conflict',
        array $translationParams = [],
        array $context = [],
        ?Throwable $previous = null
    ) {
        $this->ctx = $context;

        parent::__construct(
            $translationKey,
            $translationParams,
            0,
            $previous
        );
    }

    public function errorCode(): string
    {
        return 'CONFLICT';
    }

    public function status(): int
    {
        return 409;
    }

    public function context(): array
    {
        return $this->ctx;
    }
}
