# API REST de Solaris

La API pública está versionada bajo `/api/v1` y entrega JSON. Todas las colecciones usan la estructura de paginación de Laravel: `data`, `links` y `meta`. Las consultas aceptan `Accept: application/json`.

Base local de ejemplo: `http://127.0.0.1:8000/api/v1`.

## Endpoints

| Método | Endpoint | Parámetros | Respuesta |
|---|---|---|---|
| GET | `/departments` | Ninguno | Los 22 departamentos con id, código, nombre, coordenadas y color. |
| GET | `/farms` | `department_id`, `solar_farm_id`, `page` | Granjas paginadas con departamento, paneles, cantidades, capacidad, coordenadas y familias. |
| GET | `/farms/{id}` | Id de granja en la ruta | Ficha completa de una granja. Devuelve 404 si no existe. |
| GET | `/generations` | `department_id`, `solar_farm_id`, `from`, `to`, `page` | Registros mensuales paginados con real, esperado, CO₂, desviación y estado de alerta. |
| GET | `/statistics` | `department_id`, `solar_farm_id`, `from`, `to` | Totales, ranking departamental y tendencia mensual. |

`from` y `to` usan `YYYY-MM`. Los filtros de periodo afectan generación, esperado, CO₂, tendencia y alertas; el inventario actual de paneles, granjas y familias conserva su alcance actual. `department_id` y `solar_farm_id` sí filtran el inventario.

## Ejemplos

```bash
curl -H "Accept: application/json" http://127.0.0.1:8000/api/v1/departments
curl -H "Accept: application/json" "http://127.0.0.1:8000/api/v1/statistics?department_id=1&from=2026-01&to=2026-08"
curl -H "Accept: application/json" "http://127.0.0.1:8000/api/v1/generations?solar_farm_id=1"
```

Una respuesta de estadísticas tiene esta forma:

```json
{
  "data": {
    "totals": {
      "farms": 2,
      "panels": 30,
      "capacity_kw": 15,
      "generation_kwh": 2200,
      "expected_kwh": 2400,
      "families": 30,
      "co2_kg": 880,
      "co2_tonnes": 0.88,
      "performance": 91.67,
      "alerts": 1
    },
    "departments": [],
    "trend": []
  },
  "meta": {
    "co2_factor_kg_per_kwh": 0.4,
    "alert_threshold": 0.8
  }
}
```

## Códigos de respuesta

`200` indica consulta exitosa. `404` identifica una granja inexistente. `422` devuelve `message` y `errors` cuando un filtro tiene formato inválido o una relación no existe. `429` aparece si se supera el límite de 120 consultas por minuto y dirección IP.

Las rutas REST son de consulta. Las escrituras de la aplicación web pasan por formularios protegidos con CSRF y requieren autenticación.
