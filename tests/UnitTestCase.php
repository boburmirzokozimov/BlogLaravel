<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Base test case for unit tests that don't need database access.
 */
abstract class UnitTestCase extends BaseTestCase
{
    // Unit tests don't need RefreshDatabase
    // They should use mocks instead of actual database
}
