<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use Throwable;

final class Unauthorized extends DomainException
{
    public function __construct(
        string $translationKey = 'errors.unauthorized',
        array $translationParams = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            $translationKey,
            $translationParams,
            0,
            $previous
        );
    }

    public function errorCode(): string
    {
        return 'UNAUTHORIZED';
    }

    public function status(): int
    {
        return 401;
    }
}
