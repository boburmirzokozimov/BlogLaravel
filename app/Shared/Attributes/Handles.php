<?php

namespace App\Shared\Attributes;

use Attribute;

/**
 * Apply to a handler class to explicitly declare which
 * Command or Query it handles.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Handles
{
    /** @param class-string $messageClass */
    public function __construct(public string $messageClass)
    {
    }
}
