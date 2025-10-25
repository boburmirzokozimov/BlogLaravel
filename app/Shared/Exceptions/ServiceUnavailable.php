<?php
declare(strict_types=1);

namespace App\Shared\Exceptions;

use Throwable;

final class ServiceUnavailable extends DomainException
{
    public function __construct(
        string     $translationKey = 'errors.service_unavailable',
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
        return 'SERVICE_UNAVAILABLE';
    }

    public function status(): int
    {
        return 400;
    }
}

