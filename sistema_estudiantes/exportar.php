<?php
declare(strict_types=1);

require_once __DIR__ . '/seguridad.php';
exigirSesion();

require_once __DIR__ . '/db.php';

try {
    $resultado = $conexion->query(
        'SELECT cedula, nombre1, nombre2, apellido1, apellido2, telefono, direccion
         FROM estudiantes
         ORDER BY apellido1, nombre1'
    );
} catch (Throwable $e) {
    error_log((string)$e);
    http_response_code(500);
    exit('No se pudo generar el archivo de estudiantes.');
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="estudiantes.csv"');

$output = fopen('php://output', 'w');

if ($output === false) {
    exit('No se pudo generar el archivo.');
}

// Compatibilidad con Excel y caracteres especiales.
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, [
    'Cédula',
    'Primer nombre',
    'Segundo nombre',
    'Primer apellido',
    'Segundo apellido',
    'Teléfono',
    'Dirección'
], ';');

while ($fila = $resultado->fetch_row()) {

    // Evitar que Excel interprete datos como fórmulas.
    $fila = array_map(function ($valor) {
        $valor = (string)$valor;

        if (preg_match('/^[=+\-@\t\r]/', $valor)) {
            return "'" . $valor;
        }

        return $valor;
    }, $fila);

    fputcsv($output, $fila, ';');
}

fclose($output);
exit;
