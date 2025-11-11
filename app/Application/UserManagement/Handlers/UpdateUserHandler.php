<?php

namespace App\Application\UserManagement\Handlers;

use App\Application\UserManagement\Commands\UpdateUser;
use App\Application\UserManagement\Queries\GetUserById;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Domain\User\ValueObjects\Status;
use App\Infrastructure\User\EloquentUser;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

final readonly class UpdateUserHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $users,
        private \App\Shared\CQRS\Query\QueryBus $queryBus,
    ) {
    }

    public function __invoke(Command $command): EloquentUser
    {
        if (!$command instanceof UpdateUser) {
            throw new InvalidArgumentException(
                sprintf(
                    'UpdateUserHandler expects %s, got %s',
                    UpdateUser::class,
                    get_class($command)
                )
            );
        }

        $eloquentUser = $this->queryBus->ask(new GetUserById($command->userId));
        $user = $this->users->getById(Id::fromString($eloquentUser->id));

        if (!$user) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
        }

        $user->rename($command->name);

        // Update email if different, preserving verification status
        $newEmail = $user->email();
        if ($user->email()->value() !== $command->email) {
            $newEmail = Email::reconstitute(
                $command->email,
                $user->email()->getEmailVerifiedAt()
            );
        }

        // Update password if provided
        $newPassword = $user->password();
        if ($command->password) {
            $newPassword = PasswordHash::fromPlain($command->password);
        }

        // Reconstitute user with updated values
        $user = User::reconstitute(
            $user->id(),
            $user->name(),
            $newEmail,
            $newPassword,
            Status::fromString($command->status)
        );

        return $this->users->save($user);
    }
}
