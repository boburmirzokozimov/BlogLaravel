<?php

namespace App\Application\Handlers\User;

use App\Application\Commands\User\UpdateUser;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Domain\User\ValueObjects\Status;
use App\Infrastructure\User\EloquentUser;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

final readonly class UpdateUserHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $users,
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

        $user = $this->users->getById(Id::fromString($command->userId));

        if (!$user) {
            throw new NotFound(User::class, $command->userId);
        }

        $user->rename($command->name);

        // Update email if different, preserving verification status
        $email = $user->email();

        if (!$user->email()->isEqual(Email::fromString($command->email))) {
            $email = Email::reconstitute(
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
            $email,
            $newPassword,
            Status::fromString($command->status)
        );

        return $this->users->save($user);
    }
}
