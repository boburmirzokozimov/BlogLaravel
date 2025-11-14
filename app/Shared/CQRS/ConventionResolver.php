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
     * - App\Application\Commands\User\CreateUser → App\Application\Handlers\User\CreateUserHandler
     * - App\Application\Queries\User\GetUserById → App\Application\Handlers\User\GetUserByIdHandler
     * - App\Application\Commands\Blog\CreateBlogPost → App\Application\Handlers\Blog\CreateBlogPostHandler
     */
    public function resolveHandlerClass(Command|Query $message): string
    {
        $messageClass = get_class($message);

        // Replace Commands or Queries with Handlers, keeping the group (Blog/User/Tag)
        // App\Application\Commands\Blog\CreateBlogPost → App\Application\Handlers\Blog\CreateBlogPost
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
