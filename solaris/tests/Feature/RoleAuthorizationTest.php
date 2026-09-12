<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitors_can_read_but_cannot_modify_assets(): void
    {
        $visitor = User::factory()->create(['role' => 'visitor']);
        $this->actingAs($visitor)->get('/farms/create')->assertForbidden();
        $this->actingAs($visitor)->get('/generations/create')->assertForbidden();
    }

    public function test_technicians_can_open_asset_forms(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'technician']))->get('/farms/create')->assertOk();
    }
}
