<?php

namespace Tests\Feature;

use App\Models\ToolUsage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthUsageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_account_and_see_signed_in_dashboard(): void
    {
        $this->post('/register?lang=en', [
            'name' => 'Maya',
            'email' => 'maya@example.com',
            'password' => 'steady-start',
            'password_confirmation' => 'steady-start',
        ])
            ->assertRedirect('/?lang=en');

        $this->assertAuthenticated();

        $this->get('/dashboard?lang=en')
            ->assertOk()
            ->assertSee('Your recent tool use')
            ->assertSee('No tracked tool use yet.');
    }

    public function test_signed_in_tool_open_is_tracked_on_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/tools/focus-sprint?lang=en')
            ->assertOk();

        $this->assertDatabaseHas('tool_usages', [
            'user_id' => $user->id,
            'tool_key' => 'focus',
            'tool_label' => 'Focus sprint',
            'route_name' => 'tools.focus',
        ]);

        $this->get('/dashboard?lang=en')
            ->assertOk()
            ->assertSee('Your recent tool use')
            ->assertSee('Focus sprint')
            ->assertSee('/tools/focus-sprint?lang=en', false)
            ->assertSee('Tool opens');
    }

    public function test_login_accepts_existing_user(): void
    {
        User::factory()->create([
            'email' => 'sam@example.com',
            'password' => 'steady-start',
        ]);

        $this->post('/login?lang=en', [
            'email' => 'sam@example.com',
            'password' => 'steady-start',
        ])
            ->assertRedirect('/?lang=en');

        $this->assertAuthenticated();

        $this->assertSame(0, ToolUsage::count());
    }
}
