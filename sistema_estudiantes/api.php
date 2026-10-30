<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/seguridad.php';
require_once __DIR__ . '/db.php';

function responder(array $datos, int $codigo = 200): never {
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function limpiar(string $campo): string {
    return trim((string)($_POST[$campo] ?? ''));
}

// Comprobar que el usuario haya iniciado sesión.
if (empty($_SESSION['usuario_id'])) {
    responder(['ok' => false, 'mensaje' => 'Debes iniciar sesión.'], 401);
}

try {
    $entrada = json_decode(file_get_contents('php://input'), true);

    if (!is_array($entrada)) {
        responder(['ok' => false, 'mensaje' => 'Solicitud no válida.'], 400);
    }

    $_POST = $entrada;
    $accion = limpiar('accion');

    $rol = $_SESSION['rol'] ?? '';

    // Las operaciones de consulta están permitidas para todos los roles.
    $accionesConsulta = ['listar', 'consultar'];

    // Las operaciones de modificación requieren permisos.
    $accionesEdicion = ['guardar', 'editar', 'eliminar'];

    if (!in_array($accion, array_merge($accionesConsulta, $accionesEdicion), true)) {
        responder(['ok' => false, 'mensaje' => 'Acción desconocida.'], 400);
    }

    if (in_array($accion, $accionesEdicion, true)
        && !in_array($rol, ['administrador', 'operador'], true)) {
        responder(['ok' => false, 'mensaje' => 'No tienes permisos para realizar esta operación.'], 403);
    }

    if ($accion === 'listar') {
        $resultado = $conexion->query(
            'SELECT cedula, nombre1, nombre2, apellido1, apellido2, telefono, direccion
             FROM estudiantes
             ORDER BY apellido1, nombre1'
        );

        responder([
            'ok' => true,
            'estudiantes' => $resultado->fetch_all(MYSQLI_ASSOC)
        ]);
    }

    $cedula = limpiar('cedula');

    if ($accion === 'consultar') {
        if ($cedula === '') {
            responder(['ok' => false, 'mensaje' => 'Escribe una cédula para consultar.'], 400);
        }

        $stmt = $conexion->prepare(
            'SELECT cedula, nombre1, nombre2, apellido1, apellido2, telefono, direccion
             FROM estudiantes WHERE cedula = ?'
        );

        $stmt->bind_param('s', $cedula);
        $stmt->execute();

        $estudiante = $stmt->get_result()->fetch_assoc();

        if (!$estudiante) {
            responder(['ok' => false, 'mensaje' => 'No se encontró un estudiante con esa cédula.'], 404);
        }

        responder(['ok' => true, 'estudiante' => $estudiante]);
    }

    if ($accion === 'guardar') {
        $nombre1 = limpiar('nombre1');
        $nombre2 = limpiar('nombre2');
        $apellido1 = limpiar('apellido1');
        $apellido2 = limpiar('apellido2');
        $telefono = limpiar('telefono');
        $direccion = limpiar('direccion');

        if ($cedula === '' || $nombre1 === '' || $apellido1 === '' || $telefono === '' || $direccion === '') {
            responder(['ok' => false, 'mensaje' => 'Completa los campos obligatorios.'], 400);
        }

        $stmt = $conexion->prepare(
            'INSERT INTO estudiantes
            (cedula, nombre1, nombre2, apellido1, apellido2, telefono, direccion)
            VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->bind_param(
            'sssssss',
            $cedula,
            $nombre1,
            $nombre2,
            $apellido1,
            $apellido2,
            $telefono,
            $direccion
        );

        try {
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            if ((int)$e->getCode() === 1062) {
                responder(['ok' => false, 'mensaje' => 'Ya existe un estudiante con esa cédula.'], 409);
            }

            throw $e;
        }

        responder([
            'ok' => true,
            'mensaje' => "Estudiante $nombre1 $apellido1 guardado correctamente."
        ]);
    }

    if ($accion === 'editar') {
        $cedulaOriginal = limpiar('cedulaOriginal');
        $nombre1 = limpiar('nombre1');
        $nombre2 = limpiar('nombre2');
        $apellido1 = limpiar('apellido1');
        $apellido2 = limpiar('apellido2');
        $telefono = limpiar('telefono');
        $direccion = limpiar('direccion');

        if ($cedulaOriginal === '' || $cedula === '' || $nombre1 === '' || $apellido1 === '' || $telefono === '' || $direccion === '') {
            responder(['ok' => false, 'mensaje' => 'Completa los campos obligatorios y consulta primero al estudiante.'], 400);
        }

        $stmt = $conexion->prepare(
            'UPDATE estudiantes
             SET cedula=?, nombre1=?, nombre2=?, apellido1=?, apellido2=?, telefono=?, direccion=?
             WHERE cedula=?'
        );

        $stmt->bind_param(
            'ssssssss',
            $cedula,
            $nombre1,
            $nombre2,
            $apellido1,
            $apellido2,
            $telefono,
            $direccion,
            $cedulaOriginal
        );

        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            $check = $conexion->prepare('SELECT cedula FROM estudiantes WHERE cedula=?');
            $check->bind_param('s', $cedula);
            $check->execute();

            if (!$check->get_result()->fetch_assoc()) {
                responder(['ok' => false, 'mensaje' => 'No se encontró el estudiante para editar.'], 404);
            }
        }

        responder([
            'ok' => true,
            'mensaje' => 'Información del estudiante actualizada correctamente.'
        ]);
    }

    if ($accion === 'eliminar') {
        if ($cedula === '') {
            responder(['ok' => false, 'mensaje' => 'Escribe una cédula para eliminar.'], 400);
        }

        $stmt = $conexion->prepare('DELETE FROM estudiantes WHERE cedula=?');
        $stmt->bind_param('s', $cedula);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            responder(['ok' => false, 'mensaje' => 'No se encontró un estudiante con esa cédula.'], 404);
        }

        responder([
            'ok' => true,
            'mensaje' => 'Estudiante eliminado correctamente.'
        ]);
    }

} catch (Throwable $e) {
    error_log((string)$e);

    responder([
        'ok' => false,
        'mensaje' => 'Ocurrió un error en el servidor. Revisa la configuración de PHP y MySQL.'
    ], 500);
}
