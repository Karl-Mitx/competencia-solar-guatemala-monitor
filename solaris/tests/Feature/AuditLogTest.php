<?php
namespace Tests\Feature;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class AuditLogTest extends TestCase
{
    use RefreshDatabase;
    public function test_farm_mutations_record_actor_action_and_safe_field_names(): void
    {
        $user = User::factory()->create();
        $department = Department::create(['code'=>'01','name'=>'Guatemala','latitude'=>14.6,'longitude'=>-90.5,'color'=>'#10b981']);
        $this->actingAs($user)->post('/farms', ['name'=>'Auditable','department_id'=>$department->id,'location_name'=>'Guatemala','latitude'=>14.6,'longitude'=>-90.5,'families_count'=>2,'is_active'=>1,'panels'=>[]])->assertRedirect();
        $log = AuditLog::latest('id')->first();
        $this->assertSame($user->id, $log->user_id); $this->assertSame('created', $log->action); $this->assertContains('name', $log->changes['fields']);
    }
}
