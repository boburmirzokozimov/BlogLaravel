<?php
declare(strict_types=1);

namespace App\Shared\Exceptions;

use Throwable;

final class TooManyRequests extends DomainException
{
    public function __construct(
        string     $translationKey = 'errors.too_many_requests',
        array      $translationParams = [],
        ?Throwable $previous = null
    )
    {
        parent::__construct(
            $translationKey,
            $translationParams,
            0,
            $previous
        );
    }

    public function errorCode(): string
    {
        return 'TOO_MANY_REQUESTS';
    }

    public function status(): int
    {
        return 429;
    }
}

