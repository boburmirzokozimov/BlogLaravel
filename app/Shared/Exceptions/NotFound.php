<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use Throwable;

final class NotFound extends DomainException
{
    private string $entity;

    private string $id;

    public function __construct(
        string $entity,
        string|int $id,
        ?Throwable $previous = null
    ) {
        $this->entity = $entity;
        $this->id = (string) $id;

        parent::__construct(
            'errors.not_found',
            ['entity' => $entity, 'id' => $id],
            0,
            $previous
        );
    }

    public function errorCode(): string
    {
        return 'NOT_FOUND';
    }

    public function status(): int
    {
        return 404;
    }

    public function context(): array
    {
        return ['entity' => $this->entity, 'id' => $this->id];
    }
}
