<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use View;

/**
 * Class StatisticsControllerTest
 *
 * @package Tests\Feature
 */
class StatisticsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_statistics()
    {
        $this->withoutExceptionHandling();
        $user = factory (User::class)->create();
        View::share('user',$user);
        $response = $this->actingAs($user)->get('statistics');

        $response->assertSuccessful();
        $response->assertViewIs('statistics.show');

    }
}
