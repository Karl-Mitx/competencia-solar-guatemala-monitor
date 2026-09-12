# Solaris Guatemala

Aplicación Laravel 12 para monitorear generación solar en Guatemala. Consulta el README de la raíz para la descripción, reglas, API, pruebas y despliegue.

## Comandos

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed --force
npm ci && npm run build
php artisan serve
```

Define `DEMO_DATA=true`, `ADMIN_EMAIL` y `ADMIN_PASSWORD` para crear datos de demostración y una cuenta de administración. En producción usa MySQL o PostgreSQL, `APP_DEBUG=false`, HTTPS y variables de entorno privadas.
