<?php

namespace App\Shared\CQRS;

use App\Shared\CQRS\Command\Command;
use App\Shared\CQRS\Query\Query;
use RuntimeException;

final class ConventionResolver
{
    /**
     * Resolves the handler class name for a given command or query.
     *
     * Convention:
     * - App\Application\User\Commands\CreateUser → App\Application\User\Handlers\CreateUserHandler
     * - App\Application\User\Queries\GetUserById → App\Application\User\Handlers\GetUserByIdHandler
     */
    public function resolveHandlerClass(Command|Query $message): string
    {
        $messageClass = get_class($message);
        $messageName = class_basename($messageClass);

        // Replace Commands or Queries namespace segment with Handlers
        $handlerClass = preg_replace(
            '/\\\\(Commands|Queries)\\\\/',
            '\\Handlers\\',
            $messageClass
        );

        // Append 'Handler' suffix
        $handlerClass .= 'Handler';

        if (!class_exists($handlerClass)) {
            throw new RuntimeException(
                sprintf(
                    'Handler [%s] not found for message [%s]. '.
                    'Expected handler at: %s',
                    $handlerClass,
                    $messageClass,
                    $handlerClass
                )
            );
        }

        return $handlerClass;
    }
}
