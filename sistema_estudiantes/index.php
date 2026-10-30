
<?php

session_start();

require __DIR__ . '/db.php';

// Si ya inició sesión, entrar al panel.
if (!empty($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';

    $stmt = $conexion->prepare(
        "SELECT id, nombre, usuario, contrasena, avatar, rol
         FROM usuarios
         WHERE usuario = ? AND activo = 1
         LIMIT 1"
    );

    $stmt->bind_param('s', $usuario);
    $stmt->execute();

    $cuenta = $stmt->get_result()->fetch_assoc();

    if ($cuenta && password_verify($clave, $cuenta['contrasena'])) {

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = (int)$cuenta['id'];
        $_SESSION['nombre'] = $cuenta['nombre'];
        $_SESSION['usuario'] = $cuenta['usuario'];
        $_SESSION['avatar'] = $cuenta['avatar'];
        $_SESSION['rol'] = $cuenta['rol'];

        header('Location: dashboard.php');
        exit;

    } else {

        $mensaje = 'Usuario o contraseña incorrectos.';

    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión</title>

    <link rel="stylesheet" href="estilos.css">

</head>

<body>

    <main class="tarjeta">

       <div class="logo-uniremington">
    <img src="imagenes/logo-uniremington.png" alt="Corporación Universitaria Remington">
</div>


        <p>Bienvenido. Ingresa tus datos para continuar.</p>

        <?php if ($mensaje !== ''): ?>

            <p class="mensaje">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <label>Nombre de usuario</label>

            <input
                type="text"
                name="usuario"
                autocomplete="username"
                required
            >

            <label>Contraseña</label>

            <input
                type="password"
                name="clave"
                autocomplete="current-password"
                required
            >

            <button type="submit">
                Iniciar sesión
            </button>

        </form>

        <p>¿Todavía no tienes una cuenta?</p>

        <a href="registro.php">Registrarme</a>

    </main>

</body>

</html>

