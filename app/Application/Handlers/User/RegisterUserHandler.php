<?php

namespace App\Application\Handlers\User;

use App\Application\Commands\User\RegisterUser;
use App\Application\Events\UserRegisterEvent;
use App\Domain\Services\CacheService;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Infrastructure\User\EloquentUser;
use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Command\CommandHandler;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class RegisterUserHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $users,
        private CacheService $cache
    ) {}

    public function __invoke(Command $command): EloquentUser
    {
        if (! $command instanceof RegisterUser) {
            throw new InvalidArgumentException(
                sprintf(
                    'RegisterUserHandler expects %s, got %s',
                    RegisterUser::class,
                    get_class($command)
                )
            );
        }

        $user = User::create(
            $command->name,
            Email::fromString($command->email),
            PasswordHash::fromPlain($command->password)
        );

        // Generate verification token
        $verificationToken = Str::random(64);
        $this->cache->put(
            "email_verification:{$verificationToken}",
            $user->id()->toString(),
            now()->addDays(7) // Token expires in 7 days
        );

        UserRegisterEvent::dispatch($user, $verificationToken);

        return $this->users->save($user);
    }
}
