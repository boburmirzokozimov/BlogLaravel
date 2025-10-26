<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use Throwable;

final class InvariantViolation extends DomainException
{
    private array $ctx;

    public function __construct(
        string $translationKey = 'errors.invariant_violation',
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
        return 'INVARIANT_VIOLATION';
    }

    public function status(): int
    {
        return 422;
    }

    public function context(): array
    {
        return $this->ctx;
    }
}
