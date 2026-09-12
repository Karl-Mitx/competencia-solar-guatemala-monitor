# Comparación de granjas

La API REST de Solaris permite comparar entre dos y cuatro granjas:

```text
GET /api/v1/farms/compare?farm_ids[]=1&farm_ids[]=2
```

La respuesta incluye capacidad instalada, paneles, familias beneficiadas, generación real y esperada, rendimiento, CO₂ evitado y estado de cada instalación.

`farm_ids` es obligatorio, acepta entre 2 y 4 identificadores distintos y devuelve `422` si la selección no es válida.
