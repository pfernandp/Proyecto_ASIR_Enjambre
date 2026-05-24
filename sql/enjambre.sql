-- 1. Creación de la base de datos
CREATE DATABASE IF NOT EXISTS enjambre 
CHARACTER SET utf8 
COLLATE utf8_general_ci;

USE enjambre;

-- 2. Tabla: IA. Almacena las IAs 
CREATE TABLE IA (
    id_ia INT AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    nivel_peligro INT,
    CONSTRAINT PK_IA PRIMARY KEY (id_ia), -- Definición de clave primaria
    CONSTRAINT CHK_peligro CHECK (nivel_peligro BETWEEN 0 AND 100) 
) ENGINE=InnoDB; 

-- 3. Tabla: USUARIO (Actores: Alumno y Administrador). Gestiona la autenticación y niveles de acceso
CREATE TABLE USUARIO (
    id_usuario INT AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    grupo VARCHAR(50),
    password_hash VARCHAR(255) NOT NULL, 
    rol ENUM('Alumno', 'Administrador') DEFAULT 'Alumno', -- Diferenciación de acto-res 
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT PK_USUARIO PRIMARY KEY (id_usuario)
) ENGINE=InnoDB;

-- 4. Tabla: PRUEBA. Dependencia de existencia: cada prueba pertenece a una IA
CREATE TABLE PRUEBA (
    id_prueba INT AUTO_INCREMENT,
    id_ia INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    dificultad ENUM('Fácil', 'Media', 'Difícil', 'Final'),
    activa BOOLEAN DEFAULT FALSE, -- Control de habilitación para el administrador
    orden INT,
    fragmento_codigo TEXT NOT NULL, -- Recompensa del escape room
    CONSTRAINT PK_PRUEBA PRIMARY KEY (id_prueba),
    CONSTRAINT FK_PRUEBA_IA FOREIGN KEY (id_ia) 
        REFERENCES IA(id_ia) ON DELETE CASCADE -- Acción referencial para integridad
) ENGINE=InnoDB;

-- 5. Tabla: LOG_IA (Auditoría y Trazabilidad). Caso de uso: "Auditar logs" para su-pervisión técnica.
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

-- 6. Tabla Asociativa: ESTADO_USUARIO_PRUEBA (Progreso). Resuelve la relación Muchos a Muchos (N:M) entre Alumnos y Pruebas.
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
    CONSTRAINT UQ_USUARIO_PRUEBA UNIQUE (id_usuario, id_prueba) -- Garantiza unicidad de progreso por reto) ENGINE=InnoDB;

-- 7. Inserción de las 6 Inteligencias Artificiales antagonistas del escape room.
INSERT INTO IA (nombre, descripcion, nivel_peligro) VALUES 
('CLAVE', 'Analiza debilidades en contraseñas basándose en patrones humanos predeci-bles.', 65),
('VELO', 'Recopila información de perfiles y ubicaciones expuestas en redes socia-les.', 70),
('ANZUELO', 'Utiliza mensajes de urgencia y enlaces tentadores para capturar creden-ciales.', 85),
('RASTRO', 'Rastrea comentarios antiguos y búsquedas para reconstruir la identidad digital.', 75),
('PARÁSITO', 'Se expande a través de descargas sospechosas y aplicaciones modifica-das.', 90),
('NEXO', 'Domina las redes Wi-Fi sin protección para interceptar el tráfico de da-tos.', 95);

-- 8. Inserción de las pruebas asociadas a cada Inteligencia Artificial, configurando su orden y sus recompensas.
INSERT INTO PRUEBA (id_ia, nombre, descripcion, dificultad, activa, orden, fragmen-to_codigo) VALUES 
(1, 'La Llave Maestra', 'Crea una contraseña robusta para bloquear el acceso de CLA-VE.', 'Fácil', TRUE, 1, 'FRAG-C1-PASS'),
(2, 'Sombra Digital', 'Configura tu privacidad para desaparecer del radar de VELO.', 'Media', TRUE, 2, 'FRAG-V2-PRIV'),
(3, 'El Cebo', 'Identifica el enlace fraudulento antes de que ANZUELO te atrape.', 'Media', TRUE, 3, 'FRAG-A3-FISH'),
(4, 'Limpieza de Rastro', 'Elimina las señales digitales que RASTRO está uniendo.', 'Difícil', TRUE, 4, 'FRAG-R4-FOOT'),
(5, 'Cuarentena', 'Detecta el código malicioso oculto en la descarga de PARÁSITO.', 'Difícil', TRUE, 5, 'FRAG-P5-MALW'),
(6, 'Punto de Acceso', 'Asegura la comunicación inalámbrica interceptada por NEXO.', 'Final', TRUE, 6, 'FRAG-N6-WIFI');

-- 9. Inserción de usuarios por defecto: un administrador del sistema y un alumno de pruebas.
INSERT INTO USUARIO (nombre, grupo, password_hash, rol) VALUES 
('admin_enjambre', 'Sistemas', '$2y$12$IyvRi3BZuktFes/0Ipkv0u3aSd6aTUfzSqRFcZ3/WQdyc/36z5fKm', 'Administrador'),	
('alumno_prueba', '4º ESO A', '$2y$12$/82yML0.0Lz9A9zGKjRGf.b8dREPpOSo4vtFl.jr.hC/nrWYgOTfO', 'Alumno');

-- 10. Creación de usuario técnico y permisos estrictamente necesarios.
CREATE USER IF NOT EXISTS 'web_enjambre'@'localhost' IDENTIFIED BY 'E6_Pr0yect0_2026!';
GRANT SELECT, INSERT, UPDATE ON enjambre.* TO 'web_enjambre'@'localhost';
FLUSH PRIVILEGES;

