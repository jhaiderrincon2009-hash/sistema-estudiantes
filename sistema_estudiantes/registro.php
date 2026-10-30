
<?php

session_start();

require __DIR__ . '/db.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';
    $avatar = $_POST['avatar'] ?? '👤';

    $avatares = ['👤', '👨', '👩', '🧑‍🎓', '👨‍💻', '👩‍💻'];

    if ($nombre === '' || $usuario === '' || $clave === '') {

        $mensaje = 'Completa todos los campos.';

    } elseif (strlen($clave) < 8) {

        $mensaje = 'La contraseña debe tener mínimo 8 caracteres.';

    } elseif (!in_array($avatar, $avatares, true)) {

        $mensaje = 'Selecciona un avatar válido.';

    } else {

        $hash = password_hash($clave, PASSWORD_DEFAULT);

        try {

            $stmt = $conexion->prepare(
                'INSERT INTO usuarios (nombre, usuario, contrasena, avatar, rol)
                 VALUES (?, ?, ?, ?, "consulta")'
            );

            $stmt->bind_param(
                'ssss',
                $nombre,
                $usuario,
                $hash,
                $avatar
            );

            $stmt->execute();

            $mensaje = '¡Cuenta creada correctamente! Ya puedes iniciar sesión.';

        } catch (mysqli_sql_exception $e) {

            if ((int)$e->getCode() === 1062) {
                $mensaje = 'Ese nombre de usuario ya existe.';
            } else {
                $mensaje = 'No se pudo crear la cuenta. Revisa la conexión.';
            }

        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de usuarios</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

    <main class="tarjeta">

       <div class="logo"><span>JR</span></div> 

        <h1>Crear cuenta</h1>
        

        <p>Regístrate en el Sistema de Estudiantes.</p>

        <?php if ($mensaje !== ''): ?>

            <p class="mensaje">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <label>Nombre completo</label>
            <input type="text" name="nombre" maxlength="100" required>

            <label>Nombre de usuario</label>
            <input type="text" name="usuario" maxlength="50" required>

            <label>Contraseña</label>
            <input type="password" name="clave" minlength="8" required>

            <label>Selecciona tu avatar</label>

            <select name="avatar">
                <option value="👤">👤 Usuario</option>
                <option value="👨">👨 Hombre</option>
                <option value="👩">👩 Mujer</option>
                <option value="🧑‍🎓">🧑‍🎓 Estudiante</option>
                <option value="👨‍💻">👨‍💻 Programador</option>
                <option value="👩‍💻">👩‍💻 Programadora</option>
            </select>

            <button type="submit">Registrarme</button>

        </form>

        <p>¿Ya tienes una cuenta?</p>
        <a href="index.php">Iniciar sesión</a>

    </main>

</body>
</html>

