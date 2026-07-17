<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The owner, gov and admin panels share a single session cookie because their
 * guards (owner/gov/dalal) all use the same "users" provider. A session left on
 * one panel must never block signing in on another. These tests lock in that the
 * frontend (owner) login is not blocked by an existing gov session and vice versa.
 */
class MultiGuardLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mirror the roles that exist in production.
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
    }

    private function createOwner(): User
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'status' => 1,
            'password' => Hash::make('password'),
        ]);
        $owner->assignRole('owner');

        return $owner;
    }

    private function createGov(): User
    {
        return User::factory()->create([
            'role' => 'gov',
            'status' => 1,
            'password' => Hash::make('password'),
        ]);
    }

    public function test_owner_can_log_in_through_the_frontend(): void
    {
        $owner = $this->createOwner();

        $response = $this->post(route('frontend.login'), [
            'email' => $owner->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('owner.dashboard'));
        $this->assertTrue(auth('owner')->check());
        $this->assertSame($owner->id, auth('owner')->id());
    }

    public function test_gov_can_log_in_through_the_gov_panel(): void
    {
        $gov = $this->createGov();

        $response = $this->post(route('gov.login.submit'), [
            'email' => $gov->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('gov.dashboard'));
        $this->assertTrue(auth('gov')->check());
        $this->assertSame($gov->id, auth('gov')->id());
    }

    public function test_owner_login_is_not_blocked_by_an_existing_gov_session(): void
    {
        $owner = $this->createOwner();
        $gov = $this->createGov();

        // Sign into the gov panel first (populates the shared session cookie).
        $this->post(route('gov.login.submit'), [
            'email' => $gov->email,
            'password' => 'password',
        ]);
        $this->assertTrue(auth('gov')->check());

        // The owner must still be able to sign in through the frontend.
        $response = $this->post(route('frontend.login'), [
            'email' => $owner->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('owner.dashboard'));
        $this->assertTrue(auth('owner')->check(), 'Owner login was blocked by the existing gov session.');
        $this->assertSame($owner->id, auth('owner')->id());
    }

    public function test_gov_login_is_not_blocked_by_an_existing_owner_session(): void
    {
        $owner = $this->createOwner();
        $gov = $this->createGov();

        // Sign into the owner (frontend) panel first.
        $this->post(route('frontend.login'), [
            'email' => $owner->email,
            'password' => 'password',
        ]);
        $this->assertTrue(auth('owner')->check());

        // The gov supervisor must still be able to sign in.
        $response = $this->post(route('gov.login.submit'), [
            'email' => $gov->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('gov.dashboard'));
        $this->assertTrue(auth('gov')->check(), 'Gov login was blocked by the existing owner session.');
        $this->assertSame($gov->id, auth('gov')->id());
    }
}
