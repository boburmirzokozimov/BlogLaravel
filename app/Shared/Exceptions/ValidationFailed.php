<?php
declare(strict_types=1);

namespace App\Shared\Exceptions;

use Throwable;

final class ValidationFailed extends DomainException
{
    private array $errors;

    public function __construct(
        array      $errors = [], // ['email' => ['invalid format'], ...]
        ?Throwable $previous = null
    )
    {
        $this->errors = $errors;
        
        parent::__construct(
            'errors.validation_failed',
            [],
            0,
            $previous
        );
    }

    public function errorCode(): string
    {
        return 'VALIDATION_FAILED';
    }

    public function status(): int
    {
        return 422;
    }

    public function context(): array
    {
        return ['errors' => $this->errors];
    }
}
