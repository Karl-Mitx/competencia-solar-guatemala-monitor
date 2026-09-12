# Colaboración 50/50

Solaris se desarrolla entre Karl Tommy Escobar Castellanos y Jerelyn Andrea Marín Morales. El acuerdo del equipo es 50% de responsabilidad para cada integrante.

## Flujo de trabajo

1. Actualiza tu copia local desde la rama compartida.
2. Crea una rama descriptiva, por ejemplo `feat/filtros-reportes` o `fix/alerta-80`.
3. Haz commits pequeños que expliquen el cambio.
4. Ejecuta `php artisan test`, `vendor/bin/pint --test` y `npm run build` antes de abrir el Pull Request.
5. Abre un Pull Request y pide revisión de la otra integrante o del otro integrante.
6. Integra el cambio después de resolver los comentarios.

La cantidad de commits no se usa como una calculadora de porcentajes. El reparto 50/50 se acredita mediante acceso equivalente, tareas, revisiones, documentación y participación en la defensa.

## Reglas del repositorio

- Nunca subas `.env`, contraseñas, tokens ni archivos dentro de `.local/`.
- Mantén los datos demo identificados como sintéticos.
- Actualiza la documentación cuando cambie una ruta, cálculo o flujo de usuario.
- Antes de una entrega, comprueba la aplicación local, la API y la URL pública configurada.
