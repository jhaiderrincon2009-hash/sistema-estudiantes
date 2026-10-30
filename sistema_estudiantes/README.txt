SISTEMA DE ESTUDIANTES - PHP + MySQL + AJAX
===========================================

ARCHIVOS
- index.html: formulario y tabla.
- estilos.css: diseño.
- script.js: botones y solicitudes AJAX (fetch).
- api.php: operaciones de guardar, consultar, editar, eliminar y listar.
- db.php: conexión a MySQL.
- exportar.php: descarga de estudiantes en CSV que abre Excel.
- database.sql: crea la base de datos y tabla.

REQUISITOS
1. PHP CLI y servidor integrado (PHP 8.x).
2. MySQL o MariaDB en ejecución.
3. Extensión mysqli habilitada en PHP.

CONFIGURAR LA BASE DE DATOS
1. Abre MySQL Workbench, phpMyAdmin o tu consola MySQL.
2. Ejecuta el contenido de database.sql.
3. Si tu usuario MySQL no es root o tiene contraseña, actualiza los datos de conexión en db.php y exportar.php.

IMPORTANTE SOBRE PHP 8.5
En la carpeta PHP comprueba que exista php.ini. Si no existe:
- Copia php.ini-development y renombra la copia como php.ini.
- Abre php.ini y habilita la extensión mysqli quitando el punto y coma de la línea:
  extension=mysqli
- Asegúrate de que extension_dir apunta a la carpeta ext de PHP, normalmente:
  extension_dir = "ext"
- Guarda y abre una terminal nueva.
Para comprobarlo ejecuta:
  php -m
y verifica que aparezca mysqli.

EJECUTAR EL PROYECTO
1. Abre una terminal de VS Code en esta carpeta.
2. Ejecuta:
   php -S localhost:8000
3. Abre el navegador en:
   http://localhost:8000

No abras index.html haciendo doble clic: PHP debe ejecutarse mediante el servidor local.

BOTONES
- Guardar: registra un estudiante y evita cédulas duplicadas.
- Consultar: busca por cédula.
- Editar: actualiza el estudiante consultado/seleccionado.
- Eliminar: elimina por cédula con confirmación.
- Listar estudiantes: carga los registros de MySQL.
- Limpiar: vacía el formulario.
- Descargar Excel: descarga estudiantes.csv, compatible con Excel.

SEGURIDAD
El código usa consultas preparadas y muestra errores genéricos en el navegador.
Este proyecto es una base educativa local. Antes de publicarlo en internet, hay que agregar autenticación, autorización, protección CSRF y otras medidas de seguridad.
