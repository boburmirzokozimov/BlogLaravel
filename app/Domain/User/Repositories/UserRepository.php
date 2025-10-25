<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\UserId;
use App\Infrastructure\User\EloquentUser;

interface UserRepository
{
    public function save(User $user): EloquentUser;

    public function getById(UserId $id): ?User;

    public function getByEmail(string $email): ?User;
}
