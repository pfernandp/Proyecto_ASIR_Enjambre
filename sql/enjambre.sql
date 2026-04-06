-- 1. Creación de la base de datos
CREATE DATABASE IF NOT EXISTS enjambre 
CHARACTER SET utf8 
COLLATE utf8_general_ci;

USE enjambre;

-- 2. Tabla: IA 

CREATE TABLE IA (
    id_ia INT AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    nivel_peligro INT,
    CONSTRAINT PK_IA PRIMARY KEY (id_ia),
    CONSTRAINT CHK_peligro CHECK (nivel_peligro BETWEEN 0 AND 100) 
) ENGINE=InnoDB; 

-- 3. Tabla: USUARIO (Actores: Alumno y Administrador)

CREATE TABLE USUARIO (
    id_usuario INT AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    grupo VARCHAR(50),
    password_hash VARCHAR(255) NOT NULL, 
    rol ENUM('Alumno', 'Administrador') DEFAULT 'Alumno',  
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT PK_USUARIO PRIMARY KEY (id_usuario)
) ENGINE=InnoDB;

-- 4. Tabla: PRUEBA

CREATE TABLE PRUEBA (
    id_prueba INT AUTO_INCREMENT,
    id_ia INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    dificultad ENUM('Fácil', 'Media', 'Difícil', 'Final'),
    activa BOOLEAN DEFAULT FALSE,
    orden INT,
    fragmento_codigo TEXT NOT NULL,
    CONSTRAINT PK_PRUEBA PRIMARY KEY (id_prueba),
    CONSTRAINT FK_PRUEBA_IA FOREIGN KEY (id_ia) 
        REFERENCES IA(id_ia) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Tabla: LOG_IA

CREATE TABLE LOG_IA (
    id_log INT AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    mensaje TEXT NOT NULL,
    nivel_alerta ENUM('Bajo', 'Medio', 'Alto', 'Crítico'),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT PK_LOG_IA PRIMARY KEY (id_log),
    CONSTRAINT FK_LOG_USUARIO FOREIGN KEY (id_usuario) 
        REFERENCES USUARIO(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Tabla Asociativa: ESTADO_USUARIO_PRUEBA

CREATE TABLE ESTADO_USUARIO_PRUEBA (
    id_estado INT AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_prueba INT NOT NULL,
    completada BOOLEAN DEFAULT FALSE,
    intentos INT DEFAULT 0,
    tiempo_inicio DATETIME,
    tiempo_fin DATETIME,
    CONSTRAINT PK_ESTADO PRIMARY KEY (id_estado),
    CONSTRAINT FK_ESTADO_USUARIO FOREIGN KEY (id_usuario) 
        REFERENCES USUARIO(id_usuario) ON DELETE CASCADE,
    CONSTRAINT FK_ESTADO_PRUEBA FOREIGN KEY (id_prueba) 
        REFERENCES PRUEBA(id_prueba) ON DELETE CASCADE,
    CONSTRAINT UQ_USUARIO_PRUEBA UNIQUE (id_usuario, id_prueba)
) ENGINE=InnoDB;


USE enjambre;

-- 1. Prueba de la tabla IA

INSERT INTO IA (nombre, descripcion, nivel_peligro) VALUES 
('CLAVE', 'Especialista en accesos. Analiza debilidades en contraseñas basándose en patrones humanos predecibles como nombres y fechas.', 65),
('VELO', 'Observadora de la privacidad. Recopila información de perfiles, fotos y ubicaciones expuestas voluntariamente en redes sociales.', 70),
('ANZUELO', 'Maestra del engaño y la ingeniería social. Utiliza mensajes de urgencia y enlaces tentadores para capturar credenciales.', 85),
('RASTRO', 'La memoria del sistema. Rastrea comentarios antiguos, búsquedas y publicaciones olvidadas para reconstruir la identidad digital.', 75),
('PARÁSITO', 'Infiltrada de código malicioso. Se expande a través de descargas sospechosas, juegos "gratis" y aplicaciones modificadas.', 90),
('NEXO', 'Controladora de comunicaciones. Domina las redes abiertas y señales Wi-Fi sin protección para interceptar el tráfico de datos.', 95);

-- 2. Prueba de usuarios iniciales (Para pruebas de acceso) 

INSERT INTO USUARIO (nombre, grupo, password_hash, rol) VALUES 
('admin_enjambre', 'Sistemas', 'admin1234', 'Administrador'),
('alumno_prueba', '4º ESO A', 'alumno2026', 'Alumno');

-- 3. Inserción de una prueba de ejemplo para la IA CLAVE 

INSERT INTO PRUEBA (id_ia, nombre, descripcion, dificultad, activa, orden, fragmento_codigo) VALUES 
(1, 'La Llave Maestra', 'Crea una contraseña robusta que combine mayúsculas, números y símbolos para bloquear el acceso de CLAVE.', 'Fácil', TRUE, 1, 'FRAG-A1-XZ');
