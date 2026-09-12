@extends('layouts.app')

@section('content')
<main class="page-shell">
    <div class="page-heading"><div><span class="eyebrow">ADMINISTRACIÓN / ACCESOS</span><h1>Usuarios y roles</h1><p>Resumen de las personas con acceso al monitor Solaris.</p></div></div>
    <section class="metrics-grid" aria-label="Resumen de roles">
        <article class="metric-card"><span>Administradores</span><strong>{{ $counts['admin'] ?? 0 }}</strong><small>Configuración y control total</small></article>
        <article class="metric-card"><span>Técnicos</span><strong>{{ $counts['technician'] ?? 0 }}</strong><small>Gestión de activos y datos</small></article>
        <article class="metric-card"><span>Visitantes</span><strong>{{ $counts['visitor'] ?? 0 }}</strong><small>Consulta de información</small></article>
    </section>
    <section class="card"><div class="section-heading"><div><span class="eyebrow">DIRECTORIO</span><h2>Accesos registrados</h2></div><span class="pill neutral">{{ $users->total() }} usuarios</span></div>
        <div class="table-scroll"><table><thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Registro</th></tr></thead><tbody>
        @forelse($users as $user)<tr><td><strong>{{ $user->name }}</strong></td><td>{{ $user->email }}</td><td><span class="pill {{ $user->isAdmin() ? 'warning' : 'neutral' }}">{{ match($user->role) { 'admin' => 'Administrador', 'technician' => 'Técnico', default => 'Visitante' } }}</span></td><td>{{ $user->created_at?->format('d/m/Y') }}</td></tr>@empty<tr><td colspan="4">No hay usuarios registrados.</td></tr>@endforelse
        </tbody></table></div>{{ $users->links() }}
    </section>
</main>
@endsection
