<!DOCTYPE html><html lang="es"><body style="font-family:Arial,sans-serif;color:#183c34">
<h1>Tu reporte de SOLARIS</h1>
<p>Adjuntamos el reporte departamental solicitado desde el Atlas Solar de Guatemala.</p>
<p>Período: {{ $filters['from'] ?? 'Inicio del historial' }} a {{ $filters['to'] ?? 'Último registro' }}. El archivo respeta los filtros de departamento y granja seleccionados.</p>
@if(config('solaris.demo_data'))<p>Este reporte contiene datos de demostración.</p>@endif
<p>La generación corresponde al período consultado; la capacidad y las familias representan el inventario actual. CO₂ evitado estimado: 0.4 kg/kWh.</p>
<p>Si no solicitaste este reporte, puedes ignorar este mensaje. No te hemos suscrito a ninguna lista.</p>
</body></html>
