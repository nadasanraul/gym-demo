<?php

namespace Tests\Integration;

use App\Models\Membership;
use App\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Membership::factory()->create(['user_id' => $this->user->id]);
    }

    public function testUserCanCheckIn()
    {
        $response = $this->post("api/users/{$this->user->id}/checkin");
        $response->assertStatus(204);
    }
}
