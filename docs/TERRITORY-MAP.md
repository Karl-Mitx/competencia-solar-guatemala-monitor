# Atlas territorial

El mapa muestra los 22 departamentos con cinco intervalos iguales de generación,
calculados entre cero y el máximo departamental del filtro actual. El dorado
representa cero registrado; el gris, ausencia de registros. Un territorio fuera
del filtro está atenuado y su ficha no presenta cifras como si fueran cero.
Los valores provienen de AnalyticsService, incluidos CO₂ y capacidad; no se
calculan indicadores de negocio independientes en el navegador.

La selección funciona mediante polígonos o un selector HTML accesible. La ficha
sigue disponible si falla la descarga de Leaflet o de los límites. Los marcadores
solares conservan el acceso a la ficha de cada granja y crecen con su capacidad.
No se ha añadido un recorrido automático ni una nueva fuente de datos energéticos.

## Geografía y atribución

Fuente: MINFIN, https://github.com/minfin-bi/Mapas-TopoJSON-Guatemala/blob/main/deptos.json
Consultada el 12 de septiembre de 2026. Datos geográficos de referencia, no límites
legales certificados. La atribución también se incluye en el mapa.
El repositorio de origen no presenta una licencia explícita; se conserva la
procedencia y debe verificarse la autorización de reutilización para usos que
la requieran.

Conversión reproducible desde TopoJSON a GeoJSON, sin alterar límites salvo
redondeo a cinco decimales:

```sh
cd solaris
node scripts/convert-departments.mjs ../tmp/deptos.json
```

El archivo se distribuye como recurso local versionado por Vite, sin consultar
GitHub durante la navegación. Los mapas base siguen usando OpenStreetMap.

## Verificación

TerritoryMapTest cubre filtros mensuales, generación cero frente a ausencia de
registros, territorios excluidos, CO₂ y mapa sin inventario. La compilación Vite
incluye el GeoJSON en el manifiesto de recursos para su distribución en hosting.

Las pruebas de JavaScript (`node --test tests/js/territory-map.test.mjs`) verifican
selección, restablecimiento nacional, ausencia de datos y funcionamiento sin
Leaflet, además de los 22 códigos y el cierre y rango de los polígonos.

## Actualizar InfinityFree

Después de compilar, ejecutar `php deploy/build-map-update.php`. Se genera
`output/deploy/solaris-mapa-atlas.zip` con rutas relativas a `htdocs`.
En File Manager, abrir `htdocs`, usar Upload & Unzip y permitir reemplazar los
archivos incluidos. Luego recargar con Ctrl+F5. No contiene `.env`, credenciales,
SQL ni migraciones. No requiere reimportar la base de datos. El paquete incluye
el layout con la corrección de integridad de Leaflet y el manifiesto compilado.
