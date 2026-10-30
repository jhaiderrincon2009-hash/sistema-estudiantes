<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Comprobar que el usuario haya iniciado sesión.
function exigirSesion(): void {

    if (empty($_SESSION['usuario_id'])) {
        header('Location: index.php');
        exit;
    }

}

// Comprobar los permisos del usuario.
function exigirRol(array $roles): void {

    exigirSesion();

    if (!in_array($_SESSION['rol'] ?? '', $roles, true)) {
        http_response_code(403);
        exit('No tienes permiso para acceder a esta sección.');
    }

}