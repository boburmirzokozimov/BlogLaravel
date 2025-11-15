<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Application\Commands\User\VerifyEmail;
use App\Domain\Services\CacheService;
use App\Domain\User\Repositories\UserRepository;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use App\Shared\Exceptions\NotFound;
use App\Shared\ValueObjects\Id;
use InvalidArgumentException;

final readonly class VerifyEmailHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $users,
        private CacheService $cache
    ) {}

    public function __invoke(Command $command): mixed
    {
        if (! $command instanceof VerifyEmail) {
            throw new InvalidArgumentException(
                sprintf(
                    'VerifyEmailHandler expects %s, got %s',
                    VerifyEmail::class,
                    get_class($command)
                )
            );
        }

        // Retrieve user ID from cache using token
        $userId = $this->cache->get("email_verification:{$command->token}");

        if (! $userId) {
            throw new NotFound('Verification token', $command->token);
        }

        $user = $this->users->getById(Id::fromString($userId));

        if (! $user) {
            throw new NotFound('User', $userId);
        }

        // Verify the email
        $user->email()->activate();
        $user->activate(); // Also activate user status

        $this->users->save($user);

        // Remove the token from cache
        $this->cache->forget("email_verification:{$command->token}");

        return null;
    }
}
