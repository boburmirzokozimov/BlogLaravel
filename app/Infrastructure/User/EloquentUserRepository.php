<?php

namespace App\Infrastructure\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;

class EloquentUserRepository implements UserRepository
{

    public function save(User $user): void
    {
        EloquentUser::updateOrCreate(
            [
                'email' => $user->email()->value()
            ],
            [
                'name' => $user->name(),
                'password' => $user->password()->value()
            ]
        );
    }

    public function getById(int $id): ?User
    {
        $record = EloquentUser::where('id', $id)->first();
        if (!$record) return null;

        return new User(
            $record->name,
            new Email($record->email),
            new PasswordHash($record->password)
        );
    }

    public function getByEmail(string $email): ?User
    {
        $record = EloquentUser::where('email', $email)->first();
        if (!$record) return null;

        return new User(
            $record->name,
            new Email($record->email),
            new PasswordHash($record->password)
        );
    }
}
