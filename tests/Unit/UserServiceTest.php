<?php

namespace Tests\Unit;

use App\Models\Enums\MembershipStatus;
use App\Models\Membership;
use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;
use Exception;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private User $user;

    private Membership $membership;

    private UserService $userService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->userService = app(UserService::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->membership = Membership::factory()->create(['user_id' => $this->user->id]);
    }

    public function testUserCanCheckIn()
    {
        $this->userService->checkinUser($this->user->id);

        $this->assertDatabaseHas('invoice_lines', [
            'amount' => 1000,
        ]);
        $this->assertDatabaseHas('invoices', [
            'user_id' => $this->user->id,
            'amount' => 1000,
        ]);
        $this->assertDatabaseHas('memberships', [
            'user_id' => $this->user->id,
            'credits' => 9,
        ]);
    }

    public function testUserCannotCheckInWhenTheyHaveNoMembership()
    {
        $this->withoutExceptionHandling();

        $this->membership->delete();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No membership found!');

        $this->userService->checkinUser($this->user->id);
    }

    public function testUserCannotCheckInWhenTheyHaveNoCredits()
    {
        $this->withoutExceptionHandling();

        $this->membership->update(['credits' => 0]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You have no credits left on your membership!');

        $this->userService->checkinUser($this->user->id);
    }

    public function testUserCannotCheckInIfTheyHaveACancelledMembership()
    {
        $this->withoutExceptionHandling();

        $this->membership->status = MembershipStatus::Cancelled;
        $this->membership->save();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot check in with a cancelled membership!');

        $this->userService->checkinUser($this->user->id);
    }

    public function testUserCannotCheckInIfTheMembershipStartDateIsInTheFuture()
    {
        Carbon::setTestNow(now()->subDay());

        $this->withoutExceptionHandling();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Your membership is not started yet!');

        $this->userService->checkinUser($this->user->id);
    }

    public function testUserCannotCheckInIfTheMembershipIsExpired()
    {
        Carbon::setTestNow(now()->addYear());

        $this->withoutExceptionHandling();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Your current membership is expired!');

        $this->userService->checkinUser($this->user->id);
    }
}
