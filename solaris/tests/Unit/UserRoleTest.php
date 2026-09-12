<?php
namespace Tests\Unit;
use App\Models\User;
use PHPUnit\Framework\TestCase;
class UserRoleTest extends TestCase
{
    public function test_roles_are_explicit_and_capabilities_are_separated(): void
    {
        $this->assertTrue((new User(['role' => 'admin']))->isAdmin());
        $this->assertTrue((new User(['role' => 'technician']))->canManageAssets());
        $this->assertFalse((new User(['role' => 'visitor']))->canManageAssets());
    }
}
