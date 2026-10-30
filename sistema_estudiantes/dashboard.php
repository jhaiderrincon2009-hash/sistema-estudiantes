
<?php

require __DIR__ . '/seguridad.php';

exigirSesion();

function e($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

$rol = $_SESSION['rol'] ?? 'consulta';

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel principal</title>

    <link rel="stylesheet" href="estilos.css">

</head>

<body>

    <header class="barra">

        <div>
            <strong class="marca-jr"><span>JR</span> SISTEMA DE ESTUDIANTES</strong>
        </div>

        <div class="perfil">

            <span class="avatar">
                <?= e($_SESSION['avatar'] ?? '👤') ?>
            </span>

            <span>
                <?= e($_SESSION['nombre'] ?? 'Usuario') ?>
            </span>

            <span class="etiqueta">
                <?= e($rol) ?>
            </span>

            <a href="salir.php">Cerrar sesión</a>

        </div>

    </header>

    <main class="contenido">

        <h1>
            Bienvenido, <?= e($_SESSION['nombre'] ?? 'Usuario') ?>
        </h1>
        <p class="credito">
    PROGRAMA HECHO POR EL INGENIERO
    <strong>JHAIDER ANDRES RINCON RINCON</strong>
</p>

        <p>Has ingresado correctamente al sistema.</p>

        <section class="opciones">

            <article class="opcion">

                <h2>📚 Estudiantes</h2>

                <p>
                    Accede al sistema de registro y administración
                    de estudiantes.
                </p>

                <a href="estudiantes.php">Entrar al formulario</a>

            </article>

            <?php if ($rol === 'administrador'): ?>

                <article class="opcion">

                    <h2>👥 Usuarios</h2>

                    <p>
                        Administra las cuentas y los permisos
                        de las personas registradas.
                    </p>

                    <a href="usuarios.php">
                        Administrar usuarios
                    </a>

                </article>

            <?php endif; ?>

        </section>

    </main>

</body>

</html>

