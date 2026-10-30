<?php
require_once __DIR__ . '/seguridad.php';
exigirSesion();

$rol = $_SESSION['rol'] ?? 'consulta';
$puedeModificar = in_array($rol, ['administrador', 'operador'], true);
?>

<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estudiantes</title>
    <link rel="stylesheet" href="estilos.css">
    <script src="script.js" defer></script>
</head>
<body>
<main class="contenedor">

<header class="encabezado">
    <div>
        <p class="etiqueta">SISTEMA ACADÉMICO</p>
        <h1>Registro de estudiantes</h1>
        <p class="subtitulo">
            Registra, consulta y administra la información estudiantil.
        </p>
        <p class="subtitulo">
            Usuario: <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8') ?>
            | Rol: <?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?>
            | <a href="dashboard.php" style="color:white;">Volver al panel</a>
            | <a href="salir.php" style="color:white;">Cerrar sesión</a>
        </p>
    </div>
    <div class="insignia">SE</div>
</header>

<section class="tarjeta">
    <h2>Datos del estudiante</h2>

    <form id="formEstudiante" autocomplete="off">
        <div class="rejilla">

            <label>Cédula *
                <input type="text" id="cedula" name="cedula" maxlength="30" required>
            </label>

            <label>Primer nombre *
                <input type="text" id="nombre1" name="nombre1" maxlength="80" required>
            </label>

            <label>Segundo nombre
                <input type="text" id="nombre2" name="nombre2" maxlength="80">
            </label>

            <label>Primer apellido *
                <input type="text" id="apellido1" name="apellido1" maxlength="80" required>
            </label>

            <label>Segundo apellido
                <input type="text" id="apellido2" name="apellido2" maxlength="80">
            </label>

            <label>Teléfono *
                <input type="tel" id="telefono" name="telefono" maxlength="30" required>
            </label>

            <label class="ancho-completo">Dirección *
                <input type="text" id="direccion" name="direccion" maxlength="180" required>
            </label>

        </div>

        <div class="botones">

            <button type="button" id="btnGuardar" class="btn verde"
                <?= $puedeModificar ? '' : 'disabled' ?>>Guardar</button>

            <button type="button" id="btnConsultar" class="btn azul">
                Consultar
            </button>

            <button type="button" id="btnEditar" class="btn amarillo"
                <?= $puedeModificar ? '' : 'disabled' ?>>Editar</button>

            <button type="button" id="btnEliminar" class="btn morado"
                <?= $puedeModificar ? '' : 'disabled' ?>>Eliminar</button>

            <button type="button" id="btnListar" class="btn rojo">
                Listar estudiantes
            </button>

            <button type="button" id="btnLimpiar" class="btn gris">
                Limpiar
            </button>

            <button type="button" id="btnExcel" class="btn oscuro">
                Descargar Excel
            </button>

        </div>
    </form>

    <div id="mensaje" class="mensaje" role="status" aria-live="polite"></div>
</section>

<section class="tarjeta">
    <div class="encabezado-lista">
        <div>
            <h2>Estudiantes registrados</h2>
            <p class="subtitulo">
                Total: <strong id="totalEstudiantes">0</strong>
            </p>
        </div>

        <input id="filtroTabla" class="filtro" type="search"
            placeholder="Filtrar tabla...">
    </div>

    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>Primer nombre</th>
                    <th>Segundo nombre</th>
                    <th>Primer apellido</th>
                    <th>Segundo apellido</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                </tr>
            </thead>

            <tbody id="tablaEstudiantes">
                <tr>
                    <td colspan="7" class="vacio">
                        Cargando estudiantes...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<footer>
    Proyecto educativo · HTML, CSS, JavaScript, PHP y MySQL
</footer>


</main>
</body>
</html>
