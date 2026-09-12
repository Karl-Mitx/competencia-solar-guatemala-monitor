# Solaris Guatemala

Solaris es una aplicación web para registrar y monitorear la generación solar en los 22 departamentos de Guatemala. El sistema reúne inventario de granjas y paneles, generación mensual real y esperada, alertas de desempeño, impacto de CO₂, mapa, reportes y proyecciones explicables.

La implementación completa está en [solaris](solaris/). El proyecto utiliza Laravel 12 con PHP 8.2+, Blade, Vite, Leaflet, Chart.js y MySQL para la base de datos de entrega. SQLite se usa únicamente para las pruebas aisladas.

## Estado de la entrega

- 17 requisitos funcionales cubiertos por el código y los flujos de prueba.
- 22 departamentos, 32 granjas demo, 5 modelos de panel, 384 registros mensuales y 189 proyecciones cargadas en la base local.
- 23 pruebas pasando con 231 aserciones.
- API REST documentada bajo `/api/v1`.
- Docker Compose y Dockerfile listos para desplegar Laravel con MySQL.
- URL pública pendiente de proveedor, dominio y credenciales de despliegue.

Los datos de demostración son sintéticos y están marcados dentro de la interfaz. No representan mediciones oficiales.

## Inicio local

```powershell
cd 'C:\Competencia de Progra 2\solaris'
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Configura `.env` con un MySQL accesible, ejecuta las migraciones y carga la demo:

```powershell
php artisan migrate:fresh --seed --force
npm ci
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

En el equipo de desarrollo ya existe una instancia MySQL aislada en `127.0.0.1:3308`. Sus archivos de conexión y la contraseña local están dentro de `.local/`, excluidos de Git. No copies esos secretos a la documentación ni al repositorio.

## Navegación

La pantalla inicial muestra el panorama nacional, indicadores, tendencia, mapa y alertas. Las rutas públicas incluyen `/farms`, `/panels`, `/generations`, `/reports`, `/alerts`, `/map`, `/projections`, `/manual` y `/api-docs`. Las operaciones de alta, edición, registro y desactivación requieren iniciar sesión.

El usuario demo se crea solo si `ADMIN_EMAIL`, `ADMIN_PASSWORD` y `DEMO_DATA=true` están definidos. En desarrollo, las credenciales generadas se dejan únicamente en el archivo privado `.local/ACCESO-LOCAL.txt`.

## Reglas de negocio

- Capacidad instalada: suma de potencia nominal del panel por cantidad instalada.
- CO₂ evitado: `generación real en kWh × 0.40 kg/kWh`; las toneladas dividen el resultado entre 1,000.
- Alerta: generación real menor o igual a `80%` de la generación esperada del mismo mes.
- Proyección: promedio de generación diaria de los tres meses cerrados y consecutivos más recientes, multiplicado por los días del mes objetivo. Los pronósticos guardados son inmutables para comparar después con el valor real.
- Los periodos de generación son mensuales y deben corresponder a meses cerrados. Un valor esperado de cero no produce una desviación porcentual ni una alerta.

## API rápida

Consulta la documentación en [docs/API.md](docs/API.md) o en `/api-docs` cuando el servidor esté activo. Las consultas principales son:

```text
GET /api/v1/departments
GET /api/v1/farms?department_id=1
GET /api/v1/farms/{id}
GET /api/v1/generations?from=2026-01&to=2026-08
GET /api/v1/statistics?department_id=1
```

## Pruebas y calidad

```powershell
php artisan test
vendor/bin/pint --test
npm run build
```

La cobertura prioriza la capacidad, CO₂, umbral inclusivo de alertas, validación de periodos y coordenadas, relaciones, desactivación con historial, agregación con filtros y proyección sin fuga de datos.

## Despliegue

El directorio [solaris/deploy](solaris/deploy/) y [compose.yaml](compose.yaml) preparan un despliegue reproducible con Apache, PHP 8.3 y MySQL. Copia `.env.deploy.example` como configuración privada, genera `APP_KEY`, define contraseñas fuertes y ejecuta:

```powershell
docker compose up -d --build
```

El contenedor ejecuta migraciones y optimización en el arranque. Antes de la evaluación hay que completar un proveedor, configurar el dominio, definir HTTPS y verificar la URL desde una red externa.

## Equipo y documentos

- [Requisitos y trazabilidad](docs/REQUIREMENTS.md)
- [Arquitectura](docs/ARCHITECTURE.md)
- [API](docs/API.md)
- [Manual de usuario](docs/USER_MANUAL.md)
- [Uso de IA](docs/AI_USAGE.md)
- [Base de datos](docs/DATABASE.md)
- [Guion de demo](docs/DEMO.md)
- [Equipo](docs/TEAM.md)
- [Presentación de defensa](output/presentation/Solaris-Guatemala-v3.pptx)
- Código fuente: <https://github.com/Karl-Mitx/competencia-solar-guatemala-monitor>
