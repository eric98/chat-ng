<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class CreateTestDatabaseTest.
 * @package Tests\Unit
 */
class CreateTestDatabaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_test_database()
    {
        create_test_database();

    }
}
