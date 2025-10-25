<?php

namespace App\Shared\Attributes;

use Attribute;

/**
 * Attach this attribute to a Command or Query
 * to explicitly define its handler class.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Handler
{
    public function __construct(public string $class)
    {
    }
}
