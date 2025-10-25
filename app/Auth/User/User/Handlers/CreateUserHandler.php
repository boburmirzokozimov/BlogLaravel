<?php

namespace App\Auth\User\User\Handlers;

use App\Auth\User\User\Commands\CreateUser;
use App\Models\User;
use App\Shared\CQRS\Command;
use App\Shared\CQRS\CommandHandler;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

final class CreateUserHandler implements CommandHandler
{
    public function __invoke(Command $command): void
    {
        if (!$command instanceof CreateUser) {
            throw new InvalidArgumentException(
                sprintf(
                    'CreateUserHandler expects %s, got %s',
                    CreateUser::class,
                    get_class($command)
                )
            );
        }

        User::create([
            'name' => $command->name,
            'email' => $command->email,
            'password' => Hash::make($command->password),
        ]);
    }
}

