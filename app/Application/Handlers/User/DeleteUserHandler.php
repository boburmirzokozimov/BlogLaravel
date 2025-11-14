<?php

namespace App\Application\Handlers\User;

use App\Application\Commands\User\DeleteUser;
use App\Application\Queries\User\GetUserById;
use App\Infrastructure\User\EloquentUser;
use App\Shared\CQRS\Bus\QueryBus;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use InvalidArgumentException;

final readonly class DeleteUserHandler implements CommandHandler
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(Command $command): bool
    {
        if (!$command instanceof DeleteUser) {
            throw new InvalidArgumentException(
                sprintf(
                    'DeleteUserHandler expects %s, got %s',
                    DeleteUser::class,
                    get_class($command)
                )
            );
        }

        $user = $this->queryBus->ask(new GetUserById($command->userId));
        /* @var EloquentUser $user */
        $user->delete();

        return true;
    }
}
