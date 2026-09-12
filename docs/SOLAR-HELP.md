# Ayuda local SOLARIS

Asistente de preguntas frecuentes con clasificación por palabras clave y textos
revisados, sin modelo generativo ni llamadas a proveedores. No se presenta como
IA generativa. No incurre en tarifas de API ni requiere credenciales.

Atiende con lenguaje formal sobre navegación y conceptos de SOLARIS. No consulta
registros en tiempo real, ejecuta acciones ni envía correos; enlaza a las pantallas
correspondientes. Consultas desconocidas reciben una respuesta de alcance limitado.
El filtro de vocabulario no es exhaustivo: la garantía de salida controlada proviene
de usar exclusivamente textos revisados, nunca de repetir o generar desde la entrada.

No transmite preguntas ni las guarda. Solo las respuestas permanecen en memoria
hasta recargar, con máximo de doce mensajes. Texto insertado mediante textContent.
Control de apertura, cierre con Escape, foco devuelto al botón, etiquetas y región
de anuncios para lectores de pantalla. Disponible en ambos temas.

Pruebas: `node --test tests/js/solar-help.test.mjs`. El ZIP de tema y correo incluye
esta ayuda y se regenera con `php deploy/build-map-update.php features` tras Vite.
