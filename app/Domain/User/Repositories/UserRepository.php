<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;

interface UserRepository
{
    public function save(User $user): void;

    public function getById(int $id): ?User;

    public function getByEmail(string $email): ?User;
}
