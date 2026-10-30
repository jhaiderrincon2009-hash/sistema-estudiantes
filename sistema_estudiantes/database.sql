
CREATE DATABASE IF NOT EXISTS sistema_estudiantes
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sistema_estudiantes;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    avatar VARCHAR(30) NOT NULL DEFAULT '👤',
    rol ENUM('administrador', 'operador', 'consulta')
        NOT NULL DEFAULT 'consulta',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS estudiantes (
    cedula VARCHAR(30) PRIMARY KEY,
    nombre1 VARCHAR(80) NOT NULL,
    nombre2 VARCHAR(80) DEFAULT '',
    apellido1 VARCHAR(80) NOT NULL,
    apellido2 VARCHAR(80) DEFAULT '',
    telefono VARCHAR(30) NOT NULL,
    direccion VARCHAR(180) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);