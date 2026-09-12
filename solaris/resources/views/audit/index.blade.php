@extends('layouts.app')
@section('title','Auditoría del sistema')
@section('content')
<div class="page-heading"><div><span class="eyebrow">CONTROL Y TRAZABILIDAD</span><h1>Historial de cambios<span class="heading-dot">.</span></h1><p>Registro de acciones administrativas sobre los activos de Solaris.</p></div></div>
<section class="card"><div class="table-scroll"><table><thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Entidad</th><th>ID</th><th>Campos afectados</th></tr></thead><tbody>@forelse($logs as $log)<tr><td>{{ $log->created_at->format('d/m/Y H:i') }}</td><td>{{ $log->user?->name ?? 'Sistema' }}</td><td><span class="pill neutral">{{ ucfirst($log->action) }}</span></td><td>{{ class_basename($log->auditable_type) }}</td><td>{{ $log->auditable_id }}</td><td>{{ implode(', ', $log->changes['fields'] ?? array_keys($log->changes ?? [])) }}</td></tr>@empty<tr><td colspan="6">Aún no hay cambios registrados.</td></tr>@endforelse</tbody></table></div>{{ $logs->links() }}</section>
@endsection
