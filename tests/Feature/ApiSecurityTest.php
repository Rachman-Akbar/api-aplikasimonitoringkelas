<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_is_public(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok');
    }

    public function test_business_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/jadwal')->assertUnauthorized();
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $user = User::factory()->create(['role' => 'guru']);
        Sanctum::actingAs($user);

        $this->getJson('/api/user')->assertForbidden();
    }

    public function test_admin_can_reach_user_management_endpoint(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        $this->getJson('/api/user')->assertOk();
    }
}
