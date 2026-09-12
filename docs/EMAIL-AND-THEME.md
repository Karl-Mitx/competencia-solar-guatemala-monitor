# Apariencia y envío de reportes

El botón del menú alterna modo claro y oscuro. Recuerda la elección en el
navegador y, al entrar por primera vez, usa la preferencia del sistema.

En Reportes, cualquier visitante puede solicitar el CSV filtrado para una
dirección de correo válida. Descarga y correo usan el mismo generador.
No se aceptan rutas ni adjuntos arbitrarios. Se limita a tres solicitudes por IP
cada diez minutos, dos por destinatario por hora y cincuenta globales por hora.
El correo no se almacena como suscripción. El envío es síncrono, sin workers.

## Gmail en InfinityFree

Configurar exclusivamente en el `.env` privado del servidor, reemplazando los
valores existentes de estas variables (no duplicarlos):

```dotenv
REPORT_EMAIL_ENABLED=true
MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=tu-cuenta@gmail.com
MAIL_PASSWORD="contraseña-de-aplicación"
MAIL_FROM_ADDRESS=tu-cuenta@gmail.com
MAIL_FROM_NAME="SOLARIS Guatemala"
```

La dirección remitente debe ser la cuenta usada para autenticar. No usar la
contraseña habitual. Activar verificación en dos pasos y crear la contraseña de
aplicación en https://myaccount.google.com/apppasswords; algunas cuentas de
organizaciones no permiten esta opción. Introducirla directamente en el servidor.
No guardar secretos en Git, ZIP públicos ni mensajes.

Fuentes:
- https://support.google.com/accounts/answer/185833
- https://forum.infinityfree.com/t/emails-from-my-website-arent-working/49242

Con `REPORT_EMAIL_ENABLED=false` o un transporte `log`, la aplicación informa
que el envío no está disponible y conserva la descarga. Los errores SMTP no
se muestran ni registran con detalles sensibles. Una respuesta exitosa significa
aceptación por el transporte; no garantiza la entrega en bandeja de entrada.

Pruebas: ReportEmailTest usa transporte simulado, comprueba que el adjunto coincide
con la descarga, rechaza datos inválidos y no anuncia envío cuando falla el
transporte o no está configurado. La entrega real requiere configurar Gmail y
solicitar un reporte hacia un destinatario autorizado.

## Actualización

`php deploy/build-map-update.php features` genera
`output/deploy/solaris-tema-correo.zip`. Descomprimir en `htdocs` con reemplazo de
los archivos incluidos. Incluye el atlas previo y sus recursos compilados.
No modifica `.env`, SQL, credenciales ni almacenamiento. Después configurar el
correo como se indica arriba y recargar con Ctrl+F5.
