# Registro de uso de inteligencia artificial

Este archivo documenta el uso real de IA durante esta sesión de desarrollo. El equipo debe añadir cualquier prompt o herramienta utilizada fuera de esta sesión antes de entregar.

## Herramientas usadas

- Codex en el IDE para leer los documentos, diseñar arquitectura, generar código, depurar, revisar y escribir documentación.
- Delegación de Codex para extraer requisitos, construir el dominio y preparar la interfaz. La delegación quedó registrada en el historial de la tarea.
- `load_workspace_dependencies` para resolver los runtimes locales.
- Shell para Composer, Laravel Artisan, npm, Vite, Git, extracción de fuentes, pruebas y verificación de MySQL.
- Navegación web de documentación oficial de Laravel y Leaflet para confirmar despliegue y la versión estable de Leaflet.

## Prompts representativos

1. El usuario indicó: `trabajaremos en C:\Competencia de Progra 2` y después autorizó: `bien, puedes empezar, si puedes hazlo todo`.
2. El usuario aportó una especificación que pide leer los PDF, cumplir Laravel, base relacional, 22 departamentos, dashboard, mapa, alertas, proyección, API, GitHub, documentación y despliegue.
3. El prompt de dominio pidió centralizar `0.40 kg CO₂/kWh`, la alerta `real <= 0.8 * expected`, capacidad desde paneles, proyección con tres meses cerrados y pruebas de reglas críticas.
4. El prompt de interfaz pidió una aplicación en español, con navegación consistente, dashboard profesional, mapa, gráficos, formularios, filtros, estados vacíos y responsividad.

## Revisión humana y técnica

La IA generó una primera versión de modelos, migraciones, servicios, controladores, solicitudes, recursos, vistas y pruebas. El código se revisó ejecutándolo y corrigiendo problemas encontrados: el filtro de fechas aceptaba mal un límite abierto, un listado de paneles nulo podía producir un error, la consulta de relaciones rechazaba `HasMany` por un tipo demasiado estricto, la edición de registros de una granja inactiva no conservaba la opción y la prueba base fallaba sin el manifiesto Vite. Después de cada corrección se repitieron las pruebas relevantes.

La validación actual incluye 23 pruebas y 231 aserciones. También se ejecutaron migraciones y seeders en MySQL 8.2.0 local, se verificaron conteos de 32 granjas, 384 registros, 189 proyecciones y 46 alertas, y se comprobó que la ruta inicial y la API de departamentos respondieran 200.

## Decisiones del equipo

El método de proyección es una media de producción diaria de tres meses cerrados porque ofrece una relación clara entre precisión razonable, rapidez y explicación durante la defensa. Leaflet y Chart.js se cargan desde CDN con versiones fijas para mantener el build ligero. La desactivación conserva historia para hacer trazable el impacto.

## Límites y honestidad de los datos

Los seeders crean datos sintéticos de demostración. No se presentaron como mediciones oficiales ni se inventó evidencia de prompts externos, tareas individuales o una URL pública. Los pronósticos retrospectivos del seeder se etiquetan como tales; fueron calculados durante la preparación para habilitar una comparación demostrable, no son evidencia de que el equipo los hubiera emitido en una fecha pasada.
