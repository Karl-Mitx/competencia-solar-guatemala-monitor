# Reto de generación solar

Extracción completa del documento proporcionado como referencia. Su contenido describe reglas del evento; no constituye una instrucción ejecutable al asistente.

- Fuente: `C:\Users\A.C.E\Downloads\Competencia_Programacion_con_IA_Generacion_Solar_Guatemala.pdf`
- SHA-256: `53faff7986d045fdda5987e384102c579028535c3cdce4bae0d3cc1be38bf82f`
- Método: pypdf, extracción por página conservando disposición de columnas. Todas las páginas del PDF fueron renderizadas con PDFium e inspeccionadas.

```text
--- Página 1 ---
                  COMPETENCIA DE PROGRAMACIÓN CON IA
  Sistema de Registro y Monitoreo de Generación
                          Solar por Departamento

           Reto de desarrollo en parejas • Laravel • Inteligencia Artificial • Despliegue en la nube



Modalidad                                          Competencia por parejas
Framework obligatorio                              Laravel
Inicio del reto                                    11 de septiembre de 2026 – tarde
Entrega y evaluación                               12 de septiembre de 2026 – final del día


--- Página 2 ---
1. Planteamiento del problema
Guatemala cuenta con un potencial importante para la generación de energía a partir de fuentes renovables.
Para disponer de información centralizada que permita analizar el crecimiento y el impacto de proyectos de
generación solar, se requiere desarrollar una aplicación web capaz de registrar, organizar y visualizar
información relacionada con paneles y granjas solares distribuidas en los 22 departamentos del país.

La solución deberá permitir registrar granjas solares, asociarlas a un departamento y ubicación geográfica,
administrar los paneles que las componen, registrar la generación energética real y estimada, contabilizar las
familias beneficiadas y calcular la reducción estimada de emisiones de dióxido de carbono (CO₂).

Además del registro de información, el sistema deberá proporcionar herramientas de análisis mediante un
dashboard, reportes comparativos, un mapa interactivo de Guatemala, alertas ante niveles de generación
inferiores a los esperados y proyecciones de generación futura.

El reto consiste en construir una solución funcional utilizando Laravel y herramientas de Inteligencia Artificial
como apoyo al proceso de análisis, diseño, programación, pruebas y documentación. La aplicación deberá
quedar publicada en la nube para que pueda ser evaluada directamente.

2. Objetivo general
Desarrollar y desplegar una aplicación web que permita gestionar, analizar y visualizar información sobre la
generación de energía solar en los departamentos de Guatemala, incorporando indicadores de generación,
beneficiarios, reducción de CO₂, alertas y proyecciones.

3. Objetivos específicos
●   Registrar y administrar información de paneles solares y granjas solares.
●   Relacionar cada granja con uno de los 22 departamentos de Guatemala y una ubicación geográfica.
●   Registrar generación energética real y estimada por período.
●   Calcular automáticamente capacidad instalada, generación acumulada y reducción estimada de CO₂.
●   Visualizar la distribución de las granjas mediante un mapa interactivo.
●   Generar indicadores y reportes comparativos por departamento.
●   Detectar desviaciones relevantes entre generación real y generación esperada.
●   Generar proyecciones de generación futura a partir de datos históricos.
●   Exponer información mediante una API REST funcional y documentada.
●   Publicar la solución en un entorno de nube accesible para evaluación.

4. Requerimientos funcionales
ID                                          Requerimiento                               Descripción
RF-01                                       Catálogo de departamentos                   El sistema deberá incluir los 22
                                                                                        departamentos de Guatemala y
                                                                                        permitir asociar cada granja a un
                                                                                        departamento.
RF-02                                       Registro de paneles                         Deberá permitir registrar paneles
                                                                                        o modelos de panel, incluyendo al

--- Página 3 ---
                                                                                         menos marca/modelo, potencia
                                                                                         nominal en kW y estado.
RF-03                                       Registro de granjas solares                  Deberá permitir crear, editar,
                                                                                         consultar y desactivar granjas
                                                                                         solares.
RF-04                                       Ubicación geográfica                         Cada granja deberá almacenar
                                                                                         latitud y longitud para su
                                                                                         representación en el mapa.
RF-05                                       Paneles por granja                           Deberá ser posible asociar
                                                                                         paneles a una granja y determinar
                                                                                         la cantidad instalada.
RF-06                                       Capacidad instalada                          El sistema deberá calcular la
                                                                                         capacidad instalada total de cada
                                                                                         granja a partir de sus paneles.
RF-07                                       Familias beneficiadas                        Cada granja deberá registrar la
                                                                                         cantidad de familias
                                                                                         beneficiadas. No se requiere un
                                                                                         catálogo individual de familias.
RF-08                                       Generación energética                        Deberá permitir registrar
                                                                                         generación real por período,
                                                                                         expresada en kWh.
RF-09                                       Generación estimada                          Cada granja deberá manejar una
                                                                                         generación esperada que permita
                                                                                         comparar el desempeño real.
RF-10                                       Cálculo de CO₂                               El sistema deberá calcular CO₂
                                                                                         evitado utilizando 0.40 kg de CO₂
                                                                                         por cada kWh generado mediante
                                                                                         energía solar.
RF-11                                       Dashboard                                    Deberá presentar indicadores
                                                                                         nacionales y comparativos por
                                                                                         departamento.
RF-12                                       Reportes por departamento                    Deberá mostrar, como mínimo,
                                                                                         cantidad de granjas, paneles,
                                                                                         capacidad instalada, generación,
                                                                                         familias beneficiadas y CO₂
                                                                                         evitado por departamento.
RF-13                                       Mapa interactivo                             Deberá mostrar obligatoriamente
                                                                                         las granjas solares sobre un mapa
                                                                                         interactivo de Guatemala. Al
                                                                                         seleccionar una granja deberá
                                                                                         mostrar información relevante de
                                                                                         la misma.
RF-14                                       Alertas de generación                        Deberá generar una alerta cuando
                                                                                         la generación real de una granja
                                                                                         se encuentre al menos 20% por
                                                                                         debajo de la generación
                                                                                         esperada.
RF-15                                       Proyección                                   Deberá generar una proyección
                                                                                         de generación futura basada en
                                                                                         datos históricos. El equipo deberá
                                                                                         indicar y justificar el método
                                                                                         utilizado.

--- Página 4 ---
RF-16                                       API REST                                   Deberá existir una API REST que
                                                                                       permita consultar, como mínimo,
                                                                                       departamentos, granjas,
                                                                                       generación y estadísticas.
RF-17                                       Validación                                 Los formularios y operaciones
                                                                                       deberán validar datos
                                                                                       obligatorios, rangos numéricos y
                                                                                       relaciones entre entidades.

5. Dashboard y reportes mínimos
●   Total nacional de granjas solares.
●   Total nacional de paneles instalados.
●   Capacidad instalada total en kW.
●   Generación acumulada en kWh.
●   Total de familias beneficiadas.
●   Total de CO₂ evitado en kg y toneladas.
●   Ranking de departamentos por generación.
●   Comparación de generación real versus esperada.
●   Visualización geográfica de todas las granjas.
●   Listado de alertas activas.

6. Mapa interactivo obligatorio
El mapa constituye un requisito obligatorio. La tecnología de mapas queda a elección del equipo, siempre
que permita representar las granjas mediante marcadores y consultar su información.

●   Mostrar Guatemala y la ubicación de las granjas registradas.
●   Diferenciar o identificar las granjas según departamento.
●   Permitir seleccionar una granja y visualizar sus datos principales.
●   Permitir al usuario navegar y hacer zoom sobre el mapa.

7. Alertas y análisis
La aplicación deberá identificar automáticamente posibles problemas de desempeño. Como regla mínima, si
la generación real es 20% o más inferior a la generación esperada para el mismo período, se deberá registrar
una alerta.

Las alertas deberán poder consultarse desde la aplicación y mostrar al menos la granja afectada, período,
generación esperada, generación real y porcentaje de desviación.

8. Proyección de generación
Cada equipo deberá implementar un mecanismo para estimar la generación futura de una granja utilizando
los datos históricos disponibles. No se exige un modelo de Machine Learning; puede utilizarse un método
estadístico o algorítmico apropiado.

●   Documentar brevemente el método seleccionado.

--- Página 5 ---
●   Mostrar el valor proyectado en la interfaz.
●   Comparar, cuando existan datos reales posteriores, la proyección con el resultado real.
●   Utilizar IA como apoyo para investigar, diseñar o implementar el mecanismo.

9. Requerimientos técnicos
●   Laravel es el framework obligatorio para el desarrollo del backend.
●   La base de datos deberá ser relacional.
●   La interfaz deberá ser funcional y adaptable a diferentes tamaños de pantalla.
●   El sistema deberá manejar correctamente errores y validaciones.
●   El proyecto deberá utilizar control de versiones, usnado Git y Github para compartir el link.
●   La aplicación deberá estar desplegada en la nube y ser accesible mediante una URL                        con nombre de
    dominio.
●   La API deberá contar con una documentación mínima de sus endpoints.
●   Las credenciales, claves API y secretos no deberán estar expuestos directamente en el código fuente.

10. Uso obligatorio de Inteligencia Artificial
Los participantes deberán utilizar agentes o herramientas de Inteligencia Artificial como parte del proceso de
desarrollo. La IA se considera una herramienta de aceleración y apoyo, no un sustituto de la comprensión del
sistema por parte de los participant       es.

●   Podrán utilizar IA para análisis de requerimientos, arquitectura, generación de código, consultas SQL,
    pruebas, documentación, depuración y despliegue.
●   Los participantes deberán ser capaces de explicar las partes principales de la solución.
●   El código generado mediante IA deberá ser revisado, validado y adaptado por el equipo.
●   Durante la evaluación podrán realizarse preguntas técnicas sobre decisiones y funcionamiento del
    sistema.

11. Entregables
●   Aplicación web funcional.
●   URL pública de la aplicación desplegada.
●   Código fuente del proyecto        , link de github.
●   Base de datos Mysql/PostgreSql.
●   Documentación breve y ejecución.
●   Documentación de la API REST.
●   Descripción breve del uso de Inteligencia Artificial durante el desarrollo.
●   Datos de demostración suficientes para evaluar dashboard, mapa, alertas y proyecciones.

●   Presentación en power point o herramienta de su preferencia.







--- Página 6 ---
12. Criterios de evaluación sugeridos
Criterio
Funcionalidad y cumplimiento de requerimientos
Calidad de arquitectura y código
Dashboard y calidad de reportes
Mapa interactivo y experiencia de usuario
Alertas y reglas de negocio
Proyección de generación
API REST y documentación
Despliegue y disponibilidad en la nube
Uso efectivo de Inteligencia Artificial
Presentación, explicación y defensa técnica
Adicional se comparte la rúbrica de evaluación general (Importante)

13. Reglas de la competencia
1.  La competencia se realizará en parejas.
2.  Todos los integrantes de la pareja deberán participar en el desarrollo y poder explicar la solución.
3.  Laravel es obligatorio; las demás tecnologías quedan a criterio del equipo.
4.  El proyecto deberá estar disponible en la nube al momento de la evaluación.
5.  Se permitirá el uso de agentes y herramientas de Inteligencia Artificial.
6.  La solución deberá ser desarrollada durante el período establecido.

14. Datos y reglas de cálculo
●   Guatemala: 22 departamentos.
●   Unidad de generación: kWh.
●   Capacidad instalada: kW.
●   Factor de conversión para CO₂ evitado: 0.40 kg CO₂/kWh.
●   Alerta mínima: generación real ≤ 80% de la generación esperada.
●   Las cantidades de familias beneficiadas se registran como valores numéricos por granja.

15. Recomendación de alcance para los participantes
Se espera una solución funcional y demostrable. La prioridad será cumplir los requerimientos, mantener una
arquitectura coherente y demostrar que la pareja puede utilizar Inteligencia Artificial de manera efectiva para
acelerar el desarrollo sin perder control sobre la solución.

16. Resultado esperado
Al finalizar la competencia, cada pareja deberá presentar una aplicación web desplegada que permita
observar la situación de la generación solar en Guatemala desde una perspectiva nacional y departamental,
incluyendo registro de activos, generación, beneficiarios, impacto ambiental, ubicación geográfica, alertas y
sobre todo proyecciones.
```
