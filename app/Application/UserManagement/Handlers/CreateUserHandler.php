<?php

namespace App\Application\UserManagement\Handlers;

use App\Application\UserManagement\Commands\CreateUser;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Infrastructure\User\EloquentUser;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use InvalidArgumentException;

final readonly class CreateUserHandler implements CommandHandler
{

    public function __construct(private UserRepository $users)
    {
    }

    public function __invoke(Command $command): EloquentUser
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

        $user = User::create(
            $command->name,
            Email::fromString($command->email),
            PasswordHash::fromPlain($command->password)
        );

        $user = $this->users->save($user);

        return $user;
    }
}

