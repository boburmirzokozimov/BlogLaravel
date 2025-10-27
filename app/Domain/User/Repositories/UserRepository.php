<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;
use App\Infrastructure\User\EloquentUser;
use App\Shared\ValueObjects\Id;

interface UserRepository
{
    public function save(User $user): EloquentUser;

    public function getById(Id $id): ?User;

    public function getByEmail(string $email): ?User;
}
