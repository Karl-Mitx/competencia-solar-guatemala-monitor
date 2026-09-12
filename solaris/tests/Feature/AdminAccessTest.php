<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_view_user_directory(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'visitor']))->get('/admin/users')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'technician']))->get('/admin/users')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin/users')->assertOk()->assertSee('Usuarios y roles');
    }
}
