# Manual de usuario de Solaris

Solaris permite revisar el estado de la energía solar en Guatemala desde una pantalla pública y reservar las operaciones de escritura para usuarios autenticados.

## Entrar

Abre `/login` y escribe el correo y la contraseña de administración configurados por el equipo. Los datos de consulta, mapa y reportes permanecen disponibles sin iniciar sesión. Si no tienes sesión, los botones de escritura indican que debes ingresar.

## Registrar una granja

1. Abre `Granjas solares` y selecciona `Nueva granja`.
2. Escribe nombre, departamento, localidad y coordenadas decimales dentro de Guatemala.
3. Registra familias beneficiadas, fecha de puesta en marcha y notas.
4. Agrega uno o más modelos de panel y sus cantidades. La tarjeta lateral recalcula la capacidad en kW.
5. Guarda. La ficha conserva la relación de paneles y queda disponible en el mapa.

Para retirar una instalación usa `Desactivar granja` desde su ficha. La aplicación cambia el estado y conserva generación, alertas y proyecciones históricas.

## Administrar paneles

En `Catálogo de paneles` registra marca, modelo, potencia nominal en kW y estado. La potencia acepta hasta tres decimales. Un modelo inactivo no se puede instalar en una granja nueva, aunque sigue contribuyendo a la capacidad física de una instalación donde ya estaba asociado.

## Registrar generación

En `Generación` elige la granja, selecciona un mes cerrado y escribe la generación real y esperada en kWh. El servidor rechaza meses futuros, duplicados, cantidades negativas, relaciones inexistentes y valores que superen la capacidad física mensual. Al guardar calcula CO₂ y reevalúa la alerta del periodo.

## Leer el dashboard

`Panorama general` muestra granjas, paneles, capacidad, generación, familias, CO₂, tendencia real frente a esperada, ranking departamental, mapa y alertas. Los filtros de departamento, granja y periodo mantienen el mismo alcance en indicadores, tablas, tendencia y alertas.

## Revisar alertas

`Alertas` lista la granja, departamento, periodo, valor esperado, resultado real, desviación y estado. El umbral es inclusivo: real menor o igual al 80 % de esperado activa una alerta. Editar el registro y superar el umbral la marca como resuelta automáticamente.

## Usar el mapa

`Mapa de granjas` permite desplazarse y acercarse a Guatemala. Cada marcador usa el color de su departamento. Al seleccionarlo muestra nombre, capacidad, paneles, generación y enlace a la ficha. La aplicación muestra un aviso si el proveedor de mapas no está disponible.

## Consultar proyecciones

En `Proyecciones` selecciona una granja con al menos tres meses cerrados y consecutivos. Elige un horizonte de 1 a 12 meses y genera la estimación. Solaris promedia la producción diaria de esos tres meses y multiplica por los días de cada mes objetivo. Guarda el resultado sin sobrescribirlo cuando cambien datos posteriores; la tabla muestra el real y el error absoluto cuando ya existe.

## Reportes y API

`Reportes` compara los seis indicadores departamentales y permite descargar CSV. `API & datos` explica los endpoints y abre respuestas JSON de ejemplo. Los datos demo son sintéticos y se identifican como tales.
