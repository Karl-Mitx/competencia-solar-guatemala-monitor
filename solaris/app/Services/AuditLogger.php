<?php
namespace App\Services;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
class AuditLogger
{
    public function record(string $action, Model $model, array $changes = []): AuditLog
    {
        return AuditLog::create(['user_id' => Auth::id(), 'action' => $action, 'auditable_type' => $model::class, 'auditable_id' => $model->getKey(), 'changes' => $changes, 'ip_address' => Request::ip()]);
    }
}
