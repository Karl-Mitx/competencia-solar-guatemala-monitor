# Atlas Solar: diseño y evidencia

## Concepto

Solaris presenta la generación como una lectura territorial de Guatemala. El mapa y el balance energético comparten el espacio principal. Una franja de inventario conecta granjas, paneles, capacidad y familias con las emisiones evitadas. El comparativo departamental y las alertas permiten pasar del panorama al detalle.

La identidad usa verde profundo, superficies claras de tono mineral, cobre para las estimaciones y tipografía consistente con el proyecto. La cuadrícula del balance recuerda instrumentos de medición. El indicador circular muestra el cumplimiento de la generación esperada: el texto conserva valores superiores al 100%; el anillo se completa al alcanzar la expectativa. Sin expectativa positiva, muestra «Sin base esperada».

Las proyecciones organizan la lectura en observar, estimar y contrastar. Los próximos períodos guardados se destacan sobre la gráfica. La bitácora conserva valores estimados, reales, errores absolutos y etiquetas retrospectivas. No se incorporaron pronósticos meteorológicos, mediciones en tiempo real ni cifras ficticias fuera de los seeders existentes.

## Trazabilidad del rediseño

| Requisito o criterio | Implementación | Commit | Evidencia |
|---|---|---|---|
| Coherencia UI/UX, navegación y adaptación | `resources/css/atlas.css`, layout y navegación por teclado | `6697d9e` | Build de Vite, compilación Blade, 23 pruebas / 231 aserciones |
| RF-11, RF-12, RF-13, RF-14 | Dashboard territorial, mapa, balance, ranking y desviación obtenida del servicio de energía | `e6e4601` | Build y Blade correctos, 25 pruebas / 239 aserciones |
| RF-15 y claridad de la metodología | Horizonte de proyecciones, tabla de comparación, estado inactivo y etiquetas retrospectivas | `bc563d9` | Build y Blade correctos, 27 pruebas / 249 aserciones, Pint del archivo de pruebas |

Las pruebas de presentación están en `solaris/tests/Feature/AtlasViewTest.php`: expectativa inexistente, desviación real de alertas, conservación de filtros, proyecciones guardadas, granja inactiva e inventario vacío.

## Verificación y límites

- Las once rutas públicas de interfaz respondieron con HTTP 200 en la vista local.
- La vista local usa datos sintéticos en una base SQLite separada, `tmp/atlas-preview.sqlite`, y configuración privada `.env.atlas`. No sustituye la entrega MySQL ni demuestra despliegue público.
- Las dependencias se instalaron desde los archivos lock existentes. PHP portátil y Composer se prepararon en `tmp/runtime`, excluido de Git. La clave para ejecutar las pruebas se generó aleatoriamente en el proceso y no se versionó.
- No se efectuó inspección visual ni interacción automatizada en navegador en esta sesión. La compilación, los tests HTTP y las reglas CSS responsivas no demuestran por sí solos la calidad visual en todos los tamaños.
- Mapas, gráficas y fuentes conservan sus proveedores externos. Su carga en navegador requiere conexión.
- El concepto busca una identidad propia para Solaris; no afirma ser una interfaz sin precedentes ni garantiza una calificación.

## Continuar localmente

En el equipo de esta sesión, desde `solaris/`:

```powershell
../tmp/runtime/php/php.exe artisan serve --env=atlas --host=127.0.0.1 --port=8000 --no-reload
```

La configuración de vista local no crea credenciales de administración. Los flujos de escritura se verificaron mediante las pruebas autenticadas existentes.
