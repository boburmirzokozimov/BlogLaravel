<?php

namespace App\Application\UserManagement\Handlers;

use App\Application\UserManagement\Commands\AttachEmailToUser;
use App\Application\UserManagement\Commands\CreateUser;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\UserId;
use App\Infrastructure\User\EloquentUser;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\Exceptions\NotFound;
use InvalidArgumentException;

final readonly class AttachEmailToUserHandler implements CommandHandler
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(Command $command): EloquentUser
    {
        if (!$command instanceof AttachEmailToUser) {
            throw new InvalidArgumentException(
                sprintf(
                    'CreateUserHandler expects %s, got %s',
                    CreateUser::class,
                    get_class($command)
                )
            );
        }
        $user = $this->userRepository->getById(UserId::fromString($command->id));

        if ($user === null) {
            throw new NotFound(User::class, $command->id);
        }

        $user->attachEmail($command->email);
        $user->activate();

        return $this->userRepository->save($user);
    }
}
