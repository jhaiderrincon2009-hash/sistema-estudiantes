<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$baseDatos = 'sistema_estudiantes';

try {
    $conexion = new mysqli(
        $host,
        $usuario,
        $contrasena,
        $baseDatos
    );

    $conexion->set_charset('utf8mb4');

} catch (mysqli_sql_exception $e) {
    error_log('Error de conexión MySQL: ' . $e->getMessage());

    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Revisa la configuración de MySQL.');
}
