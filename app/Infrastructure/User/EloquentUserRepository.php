<?php

namespace App\Infrastructure\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Domain\User\ValueObjects\UserId;

class EloquentUserRepository implements UserRepository
{

    public function save(User $user): EloquentUser
    {
        return EloquentUser::updateOrCreate(
            [
                'id' => $user->id()->toString()
            ],
            [
                'name' => $user->name(),
                'email' => $user->email()->value(),
                'password' => $user->password()->value()
            ]
        );
    }

    public function getById(UserId $id): ?User
    {
        $record = EloquentUser::where('id', $id->toString())->first();
        if (!$record) return null;

        return User::reconstitute(
            UserId::fromString($record->id),
            $record->name,
            Email::reconstitute($record->email, $record->email_verified_at),
            PasswordHash::fromHash($record->password)
        );
    }

    public function getByEmail(string $email): ?User
    {
        $record = EloquentUser::where('email', $email)->first();
        if (!$record) return null;

        return User::reconstitute(
            UserId::fromString($record->id),
            $record->name,
            Email::fromString($record->email),
            PasswordHash::fromHash($record->password)
        );
    }
}
