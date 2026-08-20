<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_schedule_payload_returns_validation_error(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        $this->postJson('/api/jadwal', [])->assertUnprocessable();
    }

    public function test_register_requires_admin_role(): void
    {
        $user = User::factory()->create(['role' => 'kurikulum']);
        Sanctum::actingAs($user);

        $this->postJson('/api/auth/register', [])->assertForbidden();
    }
}
