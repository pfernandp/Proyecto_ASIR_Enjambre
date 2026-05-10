CREATE DATABASE IF NOT EXISTS enjambre CHARACTER SET utf8 COLLATE utf8_general_ci;
USE enjambre;

CREATE TABLE IA (
    id_ia INT AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    nivel_peligro INT,
    CONSTRAINT PK_IA PRIMARY KEY (id_ia),
    CONSTRAINT CHK_peligro CHECK (nivel_peligro BETWEEN 0 AND 100)
) ENGINE=InnoDB;

CREATE TABLE USUARIO (
    id_usuario INT AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    grupo VARCHAR(50),
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('Alumno', 'Administrador') DEFAULT 'Alumno',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT PK_USUARIO PRIMARY KEY (id_usuario)
) ENGINE=InnoDB;

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
    CONSTRAINT FK_PRUEBA_IA FOREIGN KEY (id_ia) REFERENCES IA(id_ia) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE LOG_IA (
    id_log INT AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    mensaje TEXT NOT NULL,
    nivel_alerta ENUM('Bajo', 'Medio', 'Alto', 'Crítico'),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT PK_LOG_IA PRIMARY KEY (id_log),
    CONSTRAINT FK_LOG_USUARIO FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ESTADO_USUARIO_PRUEBA (
    id_estado INT AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_prueba INT NOT NULL,
    completada BOOLEAN DEFAULT FALSE,
    intentos INT DEFAULT 0,
    tiempo_inicio DATETIME,
    tiempo_fin DATETIME,
    CONSTRAINT PK_ESTADO PRIMARY KEY (id_estado),
    CONSTRAINT FK_ESTADO_USUARIO FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE,
    CONSTRAINT FK_ESTADO_PRUEBA FOREIGN KEY (id_prueba) REFERENCES PRUEBA(id_prueba) ON DELETE CASCADE,
    CONSTRAINT UQ_USUARIO_PRUEBA UNIQUE (id_usuario, id_prueba)
) ENGINE=InnoDB;

INSERT INTO IA (nombre, descripcion, nivel_peligro) VALUES 
('CLAVE', 'Analiza debilidades en contraseñas basándose en patrones humanos predecibles.', 65),
('VELO', 'Recopila información de perfiles y ubicaciones expuestas en redes sociales.', 70),
('ANZUELO', 'Utiliza mensajes de urgencia y enlaces tentadores para capturar credenciales.', 85),
('RASTRO', 'Rastrea comentarios antiguos y búsquedas para reconstruir la identidad digital.', 75),
('PARÁSITO', 'Se expande a través de descargas sospechosas y aplicaciones modificadas.', 90),
('NEXO', 'Domina las redes Wi-Fi sin protección para interceptar el tráfico de datos.', 95);

INSERT INTO PRUEBA (id_ia, nombre, descripcion, dificultad, activa, orden, fragmento_codigo) VALUES 
(1, 'La Llave Maestra', 'Crea una contraseña robusta para bloquear el acceso de CLAVE.', 'Fácil', TRUE, 1, 'FRAG-C1-PASS'),
(2, 'Sombra Digital', 'Configura tu privacidad para desaparecer del radar de VELO.', 'Media', TRUE, 2, 'FRAG-V2-PRIV'),
(3, 'El Cebo', 'Identifica el enlace fraudulento antes de que ANZUELO te atrape.', 'Media', TRUE, 3, 'FRAG-A3-FISH'),
(4, 'Limpieza de Rastro', 'Elimina las señales digitales que RASTRO está uniendo.', 'Difícil', TRUE, 4, 'FRAG-R4-FOOT'),
(5, 'Cuarentena', 'Detecta el código malicioso oculto en la descarga de PARÁSITO.', 'Difícil', TRUE, 5, 'FRAG-P5-MALW'),
(6, 'Punto de Acceso', 'Asegura la comunicación inalámbrica interceptada por NEXO.', 'Final', TRUE, 6, 'FRAG-N6-WIFI');

INSERT INTO USUARIO (nombre, grupo, password_hash, rol) VALUES 
('admin_enjambre', 'Sistemas', '$2y$10$7RGE6Lx9eH1L2.dM6.QO.O2O.B5lYf/u8gY.jA9gR/U9o.l9w6.', 'Administrador'),
('alumno_prueba', '4º ESO A', '$2y$10$8Q.e.m.z.u.l.o.e.n.j.a.m.b.r.e.p.r.o.y.e.c.t.o.', 'Alumno');
