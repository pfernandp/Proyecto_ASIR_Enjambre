# El Despertar del Enjambre: Escape Room de Ciberseguridad Educativa
   
    Autor: Pedro Fernández Rodríguez
    Centro: IES Suárez de Figueroa (Zafra)
    Ciclo: CFGS Administración de Sistemas Informáticos en Red (ASIR)
--------------------------------------------------------------------------------

# Descripción del Proyecto
"El Despertar del Enjambre" es una aplicación web dinámica diseñada como herramienta de concienciación en ciberseguridad para alumnos de la ESO. 
El sistema utiliza una narrativa de ciencia ficción donde el usuario, en el rol de "Operador de Red", debe neutralizar seis Inteligencias Artificiales (IAs) hostiles que han tomado el control del sistema.
Cada entidad representa un riesgo real del entorno digital que el alumno debe resolver para obtener un fragmento de código crítico:
      
      CLAVE: Gestión de contraseñas robustas.
      VELO: Configuración de privacidad y exposición de datos.
      ANZUELO: Identificación de técnicas de Phishing.
      RASTRO: Gestión de la huella digital y metadatos.
      PARÁSITO: Detección de malware y archivos infectados.
      NEXO: Seguridad en comunicaciones inalámbricas (Wi-Fi).

--------------------------------------------------------------------------------
# Arquitectura de Sistemas (Stack LAMP)
El despliegue se basa en un modelo de tres capas para garantizar la independencia y seguridad de los datos:
  - Infraestructura: Servidor virtualizado con Oracle VM VirtualBox (v. 7.x) ejecutando Ubuntu 25.10.
  - Servidor Web: Apache2 (Puerto 80), configurado con una jerarquía de archivos modular para mejorar la seguridad lógica.
  - Nivel Interno (SGBD): MariaDB utilizando el motor transaccional InnoDB, garantizando integridad referencial y propiedades ACID (Atomicidad, Consistencia, Aislamiento y Durabilidad).
  - Lógica de Servidor: PHP 8.x, encargado de la gestión de sesiones, validación de retos y persistencia de datos mediante extensiones seguras.

--------------------------------------------------------------------------------
# Seguridad y Hardening
Como administrador de sistemas, se han aplicado medidas de robustecimiento (hardening) en el Nivel de Aplicación e Interno:
  - Prevención de Inyección SQL: Migración completa de la lógica a Sentencias Preparadas (PDO) en todos los controladores críticos (login.php, ia_detalle.php, resolver_prueba.php, verificar_final.php).
  - Seguridad Criptográfica: Almacenamiento de credenciales mediante hashes irreversibles usando la función password_hash() con algoritmo BCRYPT.
  - Estructura Modular: Aislamiento de parámetros sensibles de conexión en el directorio protegido /config/, fuera del acceso público directo.
  - Control de Acceso (ACL): Gestión de sesiones persistentes que impiden el bypass de pruebas o el acceso prematuro al Protocolo de Restauración.

--------------------------------------------------------------------------------
# Diseño de Datos y Auditoría
La base de datos enjambre sigue un modelo relacional normalizado en Tercera Forma Normal (3FN). Se compone de 5 entidades principales:
  - USUARIO: Control de identidades y roles (Alumno/Administrador).
  - IA: Catálogo de entidades antagonistas y niveles de peligro.
  - PRUEBA: Definición de retos y almacenamiento de fragmentos de código.
  - ESTADO_USUARIO_PRUEBA: Tabla asociativa que gestiona la relación N:M entre usuarios y pruebas, asegurando la atomicidad del progreso mediante ON DUPLICATE KEY UPDATE.
  - LOG_IA: Sistema de trazabilidad y auditoría de eventos de seguridad (intentos fallidos, alertas críticas) para supervisión técnica.

--------------------------------------------------------------------------------
# Instrucciones de Despliegue y Replicabilidad
Para replicar el entorno en un servidor Linux compatible, siga estos pasos administrativos:

a. Preparación de la Base de Datos
Acceda a MariaDB e importe el script consolidado que incluye el DDL y los fragmentos maestros:
    
    mysql -u root -p -e "CREATE DATABASE enjambre;"
    mysql -u root -p enjambre < sql/enjambre.sql

b. Despliegue de la Aplicación
Mueva el contenido de la carpeta /www al DocumentRoot de Apache y configure los permisos siguiendo el principio de mínimo privilegio:

    sudo cp -r www/* /var/www/
    sudo chown -R www-data:www-data /var/www/
    sudo chmod -R 755 /var/www/

c. Configuración de Conexión
Ajuste las credenciales de acceso al SGBD en el archivo /config/db.php según su entorno local.

--------------------------------------------------------------------------------
