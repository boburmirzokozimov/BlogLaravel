<?php

namespace App\Infrastructure\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Domain\User\ValueObjects\Status;
use App\Shared\ValueObjects\Id;

class EloquentUserRepository implements UserRepository
{
    public function save(User $user): EloquentUser
    {
        return EloquentUser::updateOrCreate(
            [
                'id' => $user->id()->toString(),
            ],
            [
                'name' => $user->name(),
                'email' => $user->email()->value(),
                'password' => $user->password()->value(),
                'status' => $user->status()->value(),
                'email_verified_at' => $user->email()->getEmailVerifiedAt(),
            ]
        );
    }

    public function getById(Id $id): ?User
    {
        $record = EloquentUser::where('id', $id->toString())->first();
        if (!$record) {
            return null;
        }

        return User::reconstitute(
            Id::fromString($record->id),
            $record->name,
            Email::reconstitute($record->email, $record->email_verified_at),
            PasswordHash::fromHash($record->password),
            Status::fromString($record->status)
        );
    }

    public function getByEmail(string $email): ?User
    {
        $record = EloquentUser::where('email', $email)->first();
        if (!$record) {
            return null;
        }

        return User::reconstitute(
            Id::fromString($record->id),
            $record->name,
            Email::fromString($record->email),
            PasswordHash::fromHash($record->password),
            Status::fromString($record->status)
        );
    }
}
