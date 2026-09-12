# Publicar Solaris en InfinityFree

Destino preparado: `https://mapasolargt5.gt.tc`, cuenta `if0_42898262` y base `if0_42898262_solaris` en `sql312.infinityfree.com:3306`.

## Archivos de esta entrega

En `output/deploy/`, excluido de Git:

- `solaris-infinityfree.zip`: contenido para extraer directamente dentro de `htdocs`.
- `solaris-inicial.sql`: estructura MySQL y datos sintéticos para importar solo en la base vacía.
- `ACCESO-ADMIN-PRIVADO.txt`: acceso del administrador creado por esa importación. No subir al servidor ni compartir.
- `conteos.json`: cantidades de referencia para verificar la importación.

El ZIP incluye una clave de aplicación aleatoria y una configuración `.env` con contraseña MySQL pendiente. No subirlo al repositorio ni conservar el ZIP en una carpeta pública después de extraerlo.

## Pasos en el panel

1. Configurar un certificado SSL para `mapasolargt5.gt.tc` y comprobar que HTTPS funciona. La aplicación de producción genera enlaces HTTPS y usa cookies seguras.
2. En phpMyAdmin, seleccionar `if0_42898262_solaris`, comprobar que está vacía y usar **Importar** con `solaris-inicial.sql`. El SQL no borra tablas ni registros. Si falla parcialmente, no repetir a ciegas: revisar el mensaje y las tablas creadas.
3. En el administrador de archivos, dentro de `htdocs`, usar **Upload & Unzip** con `solaris-infinityfree.zip`. Deben aparecer `app`, `bootstrap`, `public`, `vendor`, `.htaccess` y `.env` directamente dentro de `htdocs`; no dentro de otra carpeta `solaris`.
4. Editar el `.env` extraído: reemplazar únicamente `REEMPLAZAR_CON_PASSWORD_MYSQL` por la contraseña MySQL mostrada en el panel. Si contiene espacios o caracteres especiales, usar una cadena entre comillas según la sintaxis de dotenv. No enviar la contraseña por capturas ni guardarla en Git.
5. Verificar que se extrajeron los archivos ocultos `.htaccess`. La regla raíz dirige las solicitudes a `public` y bloquea carpetas privadas; cada carpeta privada incorpora además una denegación directa.
6. Abrir el dominio y comprobar dashboard, mapa, gráficas y proyecciones. Entrar con el acceso del archivo privado para verificar escritura. El SQL incluye 22 departamentos, 32 granjas, 5 modelos, 384 registros, 46 alertas y 189 proyecciones.
7. Confirmar que `/.env`, `/composer.json`, `/storage/logs/laravel.log`, `/vendor/autoload.php` y `/public/.env` no muestran archivos internos. No desactivar las protecciones para solucionar errores.

Si el administrador web no termina de extraer todos los archivos, usar el cliente FTP y subir el contenido extraído de la copia local `.local/infinityfree/htdocs`. No subir SQL, credenciales de administrador, `node_modules`, pruebas, herramientas de empaquetado ni bases SQLite. Las cachés de la copia local pueden cambiar al verificarla: preferir siempre el ZIP validado o una extracción nueva de ese ZIP para el envío.

El archivo inicial `index2.html` no es parte de Solaris. Con el `.htaccess` y `DirectoryIndex index.php` del paquete no se utiliza como inicio; no es necesario borrarlo para esta entrega.

## Preparación técnica

El script `solaris/deploy/build-infinityfree.php prepare` copia una lista explícita de carpetas, prepara directorios vacíos de almacenamiento, genera datos demo en SQLite **en memoria** y compila las migraciones reales con la gramática MySQL de Laravel. No lee ni modifica los datos de operación ni se conecta a MySQL remoto. Los textos SQL usan literales hexadecimales UTF-8 para evitar escapes dependientes del modo SQL.

Tras la preparación, instalar las dependencias del lock en la copia `.local/infinityfree/htdocs` usando `composer install --no-dev --no-scripts --prefer-dist`. Ejecutar el script con `finalize` para generar el ZIP. Se rechazan enlaces simbólicos, archivos PHP de más de 1 MB y archivos identificados como SQLite, SQL o de desarrollo. Las rutas de preparación y del ZIP deben ser nuevas: el script no sobrescribe una entrega anterior.

Para una publicación futura, conservar la clave y la configuración privada del sitio existente. No volver a importar el SQL inicial sobre una base con datos. Las modificaciones de esquema requieren una migración específica revisada, no otra carga de datos demo.

## Verificación realizada y pendiente

El ZIP contiene 6,310 archivos y aproximadamente 7.8 MB. Se verificaron su integridad, los archivos de entrada, el manifiesto Vite, la ausencia de cachés locales y la instalación sin dependencias de desarrollo. Las once rutas públicas de interfaz respondieron 200 usando las dependencias del paquete y una base demo SQLite local separada.

La importación SQL en el servidor MySQL, las reglas Apache en InfinityFree, el certificado y el funcionamiento remoto deben verificarse después de subirlo. El hosting gratuito restringe clientes externos de API: una respuesta visible en el navegador no demuestra que la API funcione desde un consumidor externo.

Referencias del proveedor: [Laravel en InfinityFree](https://forum.infinityfree.com/t/how-to-install-a-laravel-site-on-infinityfree/118578) y [limitaciones de acceso externo a API](https://forum.infinityfree.com/t/hosting-laravel-api-on-infinityfree-assistance-needed/90711).
