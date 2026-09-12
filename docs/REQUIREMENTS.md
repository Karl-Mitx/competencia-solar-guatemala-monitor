# Requisitos y trazabilidad de la competencia solar

Este documento traduce los tres documentos de referencia en criterios de implementación y entrega. No certifica que los requisitos estén implementados. Las reglas del evento se registran como datos del proyecto y se distinguen de las instrucciones directas del usuario al asistente. El usuario autorizó trabajar en `C:\Competencia de Progra 2` y comenzar el desarrollo completo.

## Fuentes y cobertura

| Código | Documento | Cobertura y extracción |
|---|---|---|
| RETO | Competencia_Programacion_con_IA_Generacion_Solar_Guatemala.pdf | 6 páginas leídas y revisadas visualmente. [Texto completo](sources/competencia-solar.md). |
| RUB | Rubrica de Evaluación.pdf | 6 páginas leídas y revisadas visualmente. La tabla se divide horizontalmente: pp. 1-2 contienen criterios y niveles 5/4; pp. 3-4 contienen niveles 3/2/1 y pesos; pp. 5-6 contienen la columna Nota Final. [Texto completo](sources/rubrica.md). |
| BASES | Bases para Competencia Dia del Programador.docx | OOXML completo del cuerpo y de todas las partes presentes de notas/comentarios/encabezados/pies. [Texto completo](sources/bases.md). No se asignan páginas al DOCX porque no se verificó su paginación con un renderizador. |

Las extracciones preservan el texto y su huella SHA-256. Los originales permanecen intactos en Downloads. No se deducen puntuaciones de las celdas vacías ni del cero final de la plantilla.

## Objetivo y alcance obligatorio

Desarrollar una aplicación web Laravel que registre y monitoree granjas y paneles solares en los 22 departamentos de Guatemala. Debe gestionar generación real y esperada, familias beneficiadas, capacidad, CO₂ evitado, indicadores nacionales y departamentales, mapa interactivo, alertas y proyecciones. Debe exponer una API REST documentada y quedar desplegada en la nube para evaluación (RETO pp. 1-6).

## Matriz de requisitos funcionales

Los identificadores RF corresponden exactamente a los del RETO. La columna de aceptación describe una comprobación práctica derivada del requisito, sin añadir una tecnología obligatoria.

| ID | Obligación | Criterio de aceptación | Fuente |
|---|---|---|---|
| RF-01 | Catálogo de los 22 departamentos y asociación de granjas. | Hay exactamente 22 departamentos de Guatemala y toda granja pertenece a uno válido. | RETO p. 2 |
| RF-02 | Registrar paneles o modelos con marca/modelo, potencia nominal en kW y estado. | Se puede guardar y consultar un modelo con esos campos; se rechazan valores inválidos. | RETO pp. 2-3 |
| RF-03 | Crear, editar, consultar y desactivar granjas. | Los cuatro flujos funcionan; desactivar conserva el registro. | RETO p. 3 |
| RF-04 | Latitud y longitud por granja. | Se guardan coordenadas válidas y se usan al dibujar el marcador. | RETO p. 3 |
| RF-05 | Asociar paneles a una granja y determinar cantidad instalada. | Una granja muestra sus modelos/cantidades y un total verificable. | RETO p. 3 |
| RF-06 | Calcular capacidad instalada por granja desde sus paneles. | La capacidad coincide con la suma de potencia nominal por cantidad instalada. | RETO p. 3 |
| RF-07 | Registrar número de familias beneficiadas por granja. | Se admiten cantidades numéricas válidas, sin requerir un catálogo de personas o familias. | RETO p. 3 |
| RF-08 | Registrar generación real por período en kWh. | Una granja admite registros históricos identificados por período. | RETO p. 3 |
| RF-09 | Manejar generación esperada para comparar desempeño. | Cada período comparado dispone de valores real y esperado compatibles. | RETO p. 3 |
| RF-10 | Calcular CO₂ evitado a 0.40 kg/kWh. | 1,000 kWh resultan en 400 kg y 0.4 toneladas de CO₂ evitado. | RETO pp. 3, 6 |
| RF-11 | Dashboard nacional y comparativo por departamento. | Los indicadores responden a los registros y permiten contrastar departamentos. | RETO pp. 3-4 |
| RF-12 | Reporte por departamento con granjas, paneles, capacidad, generación, familias y CO₂. | Los 6 indicadores están disponibles por departamento, incluidos departamentos sin registros. | RETO p. 3 |
| RF-13 | Granjas sobre mapa interactivo de Guatemala y selección con información. | Marcadores reales, navegación, zoom, identificación por departamento y datos al seleccionar. | RETO pp. 3-4 |
| RF-14 | Alerta automática cuando generación real es al menos 20% inferior a esperada. | Real 80 / esperada 100 alerta; real 80.01 / esperada 100 no alerta. Se compara el mismo período. | RETO pp. 3-4, 6 |
| RF-15 | Proyección futura basada en históricos, método indicado y justificado. | El valor futuro se muestra en interfaz; se documenta método y se compara con reales posteriores cuando existan. | RETO pp. 3-5 |
| RF-16 | API REST consultable: departamentos, granjas, generación y estadísticas como mínimo. | Los cuatro recursos devuelven datos y están documentados con endpoints y ejemplos. | RETO pp. 4-5 |
| RF-17 | Validar campos obligatorios, rangos y relaciones. | Errores entendibles ante entradas incompletas, negativas/imposibles y relaciones inexistentes. | RETO p. 4 |

## Dashboard y reportes mínimos

Según RETO p. 4 deben incluirse todos los siguientes elementos:

1. Total nacional de granjas solares.
2. Total nacional de paneles instalados.
3. Capacidad instalada total en kW.
4. Generación acumulada en kWh.
5. Total de familias beneficiadas.
6. Total de CO₂ evitado en kg y toneladas.
7. Ranking de departamentos por generación.
8. Comparación de generación real frente a esperada.
9. Visualización geográfica de todas las granjas.
10. Listado de alertas activas.

El mapa debe representar Guatemala, identificar o diferenciar granjas según departamento, mostrar datos principales al seleccionarlas y admitir desplazamiento y zoom. La biblioteca de mapas queda a elección del equipo; una imagen estática no satisface el requisito (RETO p. 4).

## Reglas de cálculo y casos límite

| Magnitud | Regla de referencia |
|---|---|
| Capacidad instalada | Suma de `potencia_nominal_kw × cantidad_instalada` para los paneles asociados. Unidad: kW. |
| Energía generada | Generación real por período en kWh; el acumulado suma períodos compatibles. |
| CO₂ evitado | `generacion_real_kwh × 0.40` en kg. Toneladas = kg / 1,000. |
| Desviación porcentual | Para esperado positivo: `(esperada − real) / esperada × 100`. |
| Alerta obligatoria | `real <= 0.80 × esperada`; el límite del 20% es inclusivo. |
| Familias | Cantidad numérica por granja, sin padrón individual. |

El RETO no define la periodicidad (diaria, mensual, etc.), ni el comportamiento con generación esperada cero, ni si los totales excluyen granjas desactivadas. Son decisiones de implementación que deben documentarse. Se recomienda mantener una periodicidad coherente, no dividir por cero y no describir un período con expectativa cero como una caída porcentual calculable. La aplicación debe hacer explícito el alcance de sus totales.

Las proyecciones pueden usar un método estadístico o algorítmico; Machine Learning no es obligatorio. Deben usar historia, justificar el método, mostrar el valor proyectado y contrastarlo con resultados posteriores cuando existan. Una media móvil o regresión explicada puede cumplir; valores aleatorios etiquetados como predicciones no cumplirían. El apoyo de IA a investigación/diseño/implementación debe documentarse (RETO pp. 4-5).

## Requisitos técnicos y entregables

| Categoría | Obligación | Fuente |
|---|---|---|
| Backend | Laravel obligatorio. | RETO pp. 1, 5-6 |
| Persistencia | Base de datos relacional; entregables nombran MySQL/PostgreSQL. | RETO p. 5 |
| Interfaz | Funcional y adaptable a diferentes tamaños de pantalla. | RETO p. 5 |
| Calidad | Manejo correcto de errores, validaciones y relaciones entre entidades. | RETO pp. 4-5 |
| Versionado | Git y GitHub con enlace al código fuente; repositorio colaborativo. | RETO p. 5; BASES §6 |
| Despliegue | Aplicación pública en la nube mediante URL con nombre de dominio, accesible al evaluar. | RETO pp. 5-6 |
| API | API funcional y documentación mínima de endpoints. | RETO pp. 4-5 |
| Secretos | Credenciales, claves API y secretos fuera del código fuente. | RETO p. 5 |
| Datos demo | Suficientes para evaluar dashboard, mapa, alertas y proyecciones. | RETO p. 5 |
| Documentación | Objetivos, instalación/ejecución, manual resumido, API, tareas y roles de cada miembro, uso de IA. | RETO p. 5; RUB p. 2 |
| Modelo de datos | Diagrama de base de datos para apoyar evaluación. | BASES §5 |
| Presentación | PowerPoint o herramienta elegida por el equipo, explicación y demostración en vivo. | RETO p. 5; RUB p. 2 |

SQLite puede ser útil para pruebas locales, pero no sustituye el entregable de base de datos MySQL/PostgreSQL especificado. Un repositorio local, configuración de Docker o un archivo de despliegue no prueban publicación efectiva: la URL y la base desplegada deben comprobarse por separado. No se inventarán un enlace GitHub, un dominio o un despliegue.

## Rúbrica general y pesos exactos

Los pesos se verificaron contra las columnas de RUB pp. 3-4 y suman 100%. Cada criterio admite niveles de 1 a 5 puntos. El PDF no muestra una fórmula de cálculo final ni una ponderación adicional a estos porcentajes; la interpretación usual `nivel / 5 × peso` no debe presentarse como fórmula oficial confirmada.

| Criterio oficial | Peso | Evidencia para el nivel de 5 puntos |
|---|---:|---|
| Originalidad de la idea, profesionalismo y grado de innovación del proyecto | 20% | Idea innovadora con enfoque único; uso adecuado de herramientas de desarrollo y control de versiones. |
| Uso de Inteligencia Artificial | 20% | IDE con agente integrado, desarrollo incremental, MCP Server, generación por lenguaje natural, LLM como asistente, prompts documentados y evidencia de uso de IA. |
| Aspectos de diseño de la UI/UX | 15% | Interfaz intuitiva, coherente entre pantallas, usable, minimalista y/o innovadora; colores adecuados, tipografía consistente, diseño responsivo. |
| Funcionalidad y cumplimiento de los requerimientos | 25% | Funciones principales y secundarias sin errores. |
| Documentación asociada al proyecto | 10% | Objetivos, manual resumido, tareas y rol de cada integrante, commits claros. |
| Presentación del proyecto, claridad y orden de ideas | 10% | Exposición clara y estructurada, buena comunicación, recursos visuales y demostración en vivo fluida. |

El nivel 4 de IA todavía nombra MCP, pero carece de prompts documentados y evidencia. El nivel 3 nombra IDE/agente/desarrollo incremental y omite MCP, documentación de prompts y evidencia. Los niveles 2 y 1 reducen los elementos de integración y evidencia. Conviene conservar evidencia real de cada herramienta efectivamente usada; no afirmar que una configuración MCP vacía prueba su uso.

RETO p. 6 ofrece además una lista de criterios sugeridos, sin pesos: funcionalidad; arquitectura/código; dashboard/reportes; mapa/UX; alertas/reglas; proyección; API/documentación; despliegue; IA; presentación/defensa. Remite expresamente a la rúbrica general como importante. No se deben asignar porcentajes inventados a esos diez elementos.

## Calendario y reglas de participación

| Asunto | Texto establecido |
|---|---|
| Inicio del reto específico | 11 de septiembre de 2026, tarde. RETO p. 1. |
| Publicación según bases generales | El viernes a las 17 horas, el día antes de la competencia. BASES §3. |
| Entrega y evaluación | 12 de septiembre de 2026, final del día. RETO p. 1; no indica hora exacta. |
| Etapa remota | Duración recomendada de 5 horas. BASES §4. |
| Etapa presencial | 2 horas para desarrollar/completar. BASES §4. |
| Presentación | 10 minutos finales después del tiempo de trabajo. BASES §4. |
| Registro remoto | Check-in inicial por Google Meet o similar; no hace falta permanecer conectado toda la jornada. BASES §4.1. |
| Evidencia remota | Commits y fotos del avance. Su incumplimiento puede reducir puntos o causar descalificación según gravedad. BASES §4.1. |
| Participantes | Estudiantes de Ingeniería en Sistemas UMG, sede Puerto Barrios. BASES §1. |
| Trabajo | Realizado durante el tiempo de competencia; no copiar código de otros participantes; no incorporar otros miembros. BASES §6 y RETO p. 6. |
| Comprensión | Ambos integrantes deben participar y poder explicar la solución; se pueden hacer preguntas técnicas. RETO pp. 5-6. |
| IA | Uso requerido como apoyo; el código generado debe revisarse, validarse y adaptarse por el equipo. RETO p. 5; BASES §§2, 6. |

El desarrollo asistido no demuestra presencia, participación humana, revisión del equipo ni cumplimiento del check-in. Estos hechos requieren evidencia auténtica de los participantes. Los roles y nombres no deben inventarse.

## Conflictos y decisiones de interpretación

| Tema | Documentos | Tratamiento para este proyecto |
|---|---|---|
| Framework libre frente a Laravel | BASES §2 permite cualquier framework/lenguaje; RETO exige Laravel. | Aplicar Laravel por ser el requisito específico del reto. |
| Individual frente a pareja | BASES §1 admite individuales y máximo 2; RETO pp. 1, 6 exige parejas. | No asumir una dispensa individual. El software puede desarrollarse; elegibilidad y composición deben confirmarse con organizadores. |
| Cronograma general frente a fechas | BASES fija viernes 17 h, 5 h remotas recomendadas, 2 h presenciales y demo; RETO fija 11-12 sep 2026 y fin del día. | Conservar ambas referencias; no deducir una hora exacta de cierre o que todo el intervalo esté habilitado. |
| Motor relacional frente a entregable | RETO requisitos técnicos dicen relacional; entregables dicen MySQL/PostgreSQL. | Elegir MySQL o PostgreSQL para la entrega; documentar cualquier motor local auxiliar. |
| Rúbrica mencionada como Excel | BASES refiere una rúbrica Excel; el adjunto recibido es PDF. | Usar los pesos verificables del PDF recibido sin inventar contenido de un Excel no proporcionado. |
| Criterios sugeridos frente a pesos | RETO lista 10 criterios sin pesos; RUB asigna pesos a 6. | Mantener la lista de cobertura técnica y la rúbrica ponderada por separado. |

## Evidencia de entrega a recopilar

- URL pública real y prueba de acceso externo.
- Enlace GitHub real y commits descriptivos que representen el avance.
- Motor de base de datos y migraciones reproducibles.
- Demostración de los 17 RF, incluidos el umbral exacto de alerta y una proyección comparada contra resultados posteriores.
- Registro de prompts y herramientas de IA usadas, con revisión/validación efectivamente realizada.
- Nombres, tareas y roles reales de los participantes; evidencia de check-in y fotos cuando aplique.
- Manual, diagrama de datos, API documentada y presentación ensayable en 10 minutos.

Esta lista permite verificar hechos de entrega; no marca como completadas actividades externas todavía no verificadas.
