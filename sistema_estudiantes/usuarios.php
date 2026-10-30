
<?php

require __DIR__ . '/seguridad.php';
exigirRol(['administrador']);

require_once __DIR__ . '/db.php';

$mensaje = '';

$rolesPermitidos = [
    'administrador',
    'operador',
    'consulta'
];

// Procesar cambios de usuarios.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $rol = $_POST['rol'] ?? '';
    $activo = ($_POST['activo'] ?? '') === '1' ? 1 : 0;

    if (!$id || !in_array($rol, $rolesPermitidos, true)) {

        $mensaje = 'Los datos enviados no son válidos.';

    } elseif ($id === (int)$_SESSION['usuario_id']) {

        $mensaje = 'No puedes modificar tu propia cuenta.';

    } else {

        $stmt = $conexion->prepare(
            'UPDATE usuarios SET rol = ?, activo = ? WHERE id = ?'
        );

        $stmt->bind_param('sii', $rol, $activo, $id);
        $stmt->execute();

        $mensaje = 'Usuario actualizado correctamente.';
    }
}

// Consultar usuarios.
$resultado = $conexion->query(
    'SELECT id, nombre, usuario, avatar, rol, activo, creado_en
     FROM usuarios
     ORDER BY id DESC'
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administrar usuarios</title>

    <link rel="stylesheet" href="estilos.css">

</head>

<body>

    <main class="contenido">

        <h1>Administración de usuarios</h1>

        <p>
            Aquí puedes gestionar las cuentas registradas.
        </p>

        <p>
            <a href="dashboard.php">← Volver al panel principal</a>
        </p>

        <?php if ($mensaje !== ''): ?>

            <p class="mensaje">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
            </p>

        <?php endif; ?>

        <div class="tabla-contenedor">

            <table>

                <thead>

                    <tr>
                        <th>Avatar</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol y estado</th>
                        <th>Acción</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($fila = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($fila['avatar'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($fila['usuario'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>

                                <?php if ((int)$fila['id'] === (int)$_SESSION['usuario_id']): ?>

                                    <?= htmlspecialchars($fila['rol'], ENT_QUOTES, 'UTF-8') ?>
                                    (Tu cuenta)

                                <?php else: ?>

                                    <form method="POST" class="form-fila">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$fila['id'] ?>"
                                        >

                                        <select name="rol">

                                            <?php foreach ($rolesPermitidos as $r): ?>

                                                <option
                                                    value="<?= $r ?>"
                                                    <?= $fila['rol'] === $r ? 'selected' : '' ?>
                                                >
                                                    <?= $r ?>
                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                        <select name="activo">

                                            <option value="1"
                                                <?= (int)$fila['activo'] === 1 ? 'selected' : '' ?>>
                                                Activo
                                            </option>

                                            <option value="0"
                                                <?= (int)$fila['activo'] === 0 ? 'selected' : '' ?>>
                                                Inactivo
                                            </option>

                                        </select>

                            </td>

                            <td>

                                        <button type="submit">
                                            Guardar cambios
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </main>

</body>

</html>

