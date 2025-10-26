<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use Throwable;

final class BadRequest extends DomainException
{
    /**
     * @param string $translationKey
     * @param array $translationParams
     * @param Throwable|null $previous
     */
    public function __construct(
        string $translationKey = 'errors.bad_request',
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
        return 'BAD_REQUEST';
    }

    public function status(): int
    {
        return 400;
    }
}
