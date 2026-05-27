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

# Instrucciones de Despliegue y Replicabilidad (Arquitectura Docker)

Requisitos Previos del Sistema Anfitrión

Antes de iniciar el despliegue, comprobaremos que el sistema anfitrión cumple los siguientes requi-sitos:

	- Sistema operativo: GNU/Linux (recomendado Ubuntu Server 22.04 LTS o superior).
	- Docker Engine: instalado y con el demonio activo (sudo systemctl status docker).
	- MariaDB Server 10.x: instalado en el anfitrión y en ejecución. Actuará como nivel interno de persistencia, accesible desde el contenedor a través del gateway del bridge de Docker (172.17.0.1).

Instalamos Docker Engine si no está disponible:
      
      sudo apt update && sudo apt install -y docker.io
      sudo systemctl enable --now docker

1. Preparación del Nivel Interno (MariaDB en el Anfitrión)

En esta fase configuramos el motor de base de datos del anfitrión para que acepte conexiones desde la subred virtual de Docker y cargamos el esquema completo del sistema. Se ejecutan dos bloques de comandos: uno de importación y otro de hardening de acceso.

A. Importacion del esquema DDL/DML

Cargamos el script consolidado enjambre.sql, que contiene la definición completa de las tablas, las restricciones de integridad referencial, los datos iniciales y la creación del usuario restringido web_enjambre:

# Crear la base de datos receptora
      
      mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS enjambre;"
# Importar el esquema completo (DDL + DML + usuario restringido)
      
      mysql -u root -p enjambre < /ruta/al/proyecto/sql/enjambre.sql
   
Verificamos la carga correcta antes de continuar:
      
      mysql -u root -p -e "USE enjambre; SHOW TABLES; SELECT nombre FROM IA;"

La salida debe listar las cinco tablas del modelo (IA, USUARIO, PRUEBA, ESTA-DO_USUARIO_PRUEBA, LOG_IA) y los seis registros de entidades antagonistas.

B. Hardening de acceso: autorizacion desde la subred Docker

El contenedor web se comunica con MariaDB a traves de la IP del gateway del bridge de Docker (172.17.0.1). Para que el motor acepte estas conexiones, es necesario otorgar permisos DCL al usuario web_enjambre desde el rango de direcciones de la subred virtual:

# Acceder a la consola de administración de MariaDB
      
      mysql -u root -p

-- Autorizar al usuario técnico desde la subred del bridge de Docker

      GRANT SELECT, INSERT, UPDATE ON enjambre.* TO 'web_enjambre'@'172.17.%.%' IDENTIFIED BY 'E6_Pr0yect0_2026!';

-- Aplicar los cambios en la tabla de privilegios

      FLUSH PRIVILEGES;

-- Verificar que el permiso ha sido registrado

      SHOW GRANTS FOR 'web_enjambre'@'172.17.%.%';

C. Hardening de red: escucha en la interfaz bridge

Por defecto, MariaDB solo escucha en 127.0.0.1 (loopback). Para que el contenedor pueda alcanzar el motor, es necesario configurar el servidor para que escuche también en la interfaz del bridge de Docker. Editamos el archivo de configuración del SGBD:

      sudo nano /etc/mysql/mariadb.conf.d/50-server.cnf

Localizamos la directiva bind-address y la modificamos para que escuche en todas las interfaces:

# Antes:

      bind-address = 127.0.0.1

# Despues:
      bind-address = 0.0.0.0

Aplicamos el cambio reiniciando el servicio:

      sudo systemctl restart mariadb

Si el sistema anfitrión dispone de firewall (ufw), abrimos el puerto 3306 exclusivamente para la su-bred de Docker:

      sudo ufw allow from 172.17.0.0/16 to any port 3306

2. Jerarquía de Archivos del Proyecto

Antes de construir la imagen Docker, comprobaremos que la estructura de directorios del proyecto local sigue el esquema modular requerido por el Dockerfile. La organización segrega los activos criptográficos, la configuración de Apache, el nodo de conexión al SGBD y el código fuente de la aplicación en rutas independientes:

Proyecto_Enjambre/                         <- Raíz del proyecto
|
|-- Dockerfile                    <- Infraestructura como Código
|
|-- config/
|   |-- apache/
|   |   |-- 000-default.conf     <- Virtual Host HTTP (redirección 301 a HTTPS)
|   |   +-- default-ssl.conf     <- Virtual Host HTTPS (puerto 443)
|   |
|   |-- php/
|   |   +-- db.php               <- Nodo de conexión ($host = '172.17.0.1')
|   |
|   +-- ssl/
|       |-- server.crt           <- Certificado X.509 autofirmado (RSA 4096 bits)
|       +-- server.key           <- Clave privada RSA (chmod 600)
|
|-- sql/
|   +-- enjambre.sql             <- Script DDL/DML consolidado
|
+-- www/                         <- Código fuente PHP (DocumentRoot)
    |-- index.php
    |-- login.php
    |-- registro.php
    |-- dashboard.php
    |-- ia_detalle.php
    |-- resolver_prueba.php
    |-- protocolo_final.php
    |-- verificar_final.php
    |-- victoria.php
    |-- logout.php
    |-- manual.php
    |-- logo.png
    |-- archivos de audio
    +-- Imagenes

Aspectos críticos de esta jerarquía:

	- config/php/db.php: el parámetro $host debe estar configurado como '172.17.0.1' (gateway del bridge de Docker hacia el anfitrión), no como 'localhost'. Verifiamos este valor antes de construir la imagen.
	
	- config/ssl/: los activos criptográficos deben existir en el repositorio local. Si aún no se han generado, nos situamos en el directorio /config/ssl ejecutamos los comandos: 
	
		openssl genrsa -out server.key 4096 
		openssl req -new -x509 -days 365 -key server.key -out server.crt

	- www/ (raiz plana): todos los archivos PHP deben estar directamente en la raíz de esta carpe-ta, sin subdirectorios adicionales, para que el Dockerfile los mapee correctamente al DocumentRoot de Apache (/var/www/html/).

	- Rutas de require_once: todos los scripts PHP que conectan al SGBD deben referenciar el nodo de conexión mediante ruta relativa: require_once('../config/db.php');. Esto garantiza que la ruta sea correcta tanto en el entorno Docker como en un despliegue LAMP tradicional.

3. El Dockerfile

El archivo Dockerfile, ubicado en la raíz del proyecto, automatiza la construcción de una imagen inmutable basada en php:8.2-apache que incluye todas las dependencias, configuraciones y activos de seguridad del sistema.

4. Ciclo de Despliegue y Lanzamiento

Desde la terminal, situado en la carpeta raíz del proyecto (donde se encuentra el Dockerfile), ejecutamos la siguiente secuencia. El flag --no-cache garantiza que Docker no reutilice capas de compilaciones anteriores, asegurando que la imagen final refleja exactamente el estado actual del repositorio:

# Paso 1: Construir la imagen inmutable desde el Dockerfile

      sudo docker build --no-cache -t enjambre-seguro .

# Paso 2: Lanzar el contenedor vinculando los puertos estándar del anfitrión

      sudo docker run -d -p 80:80 -p 443:443 --name enjambre-servidor enjambre-seguro

El flag -d ejecuta el contenedor en modo detached (segundo plano). Los mapeos -p 80:80 y -p 443:443 vinculan los puertos del anfitrión con los del contenedor, permitiendo que los clientes de la red local accedan al escape room a través de la IP del servidor anfitrión.

5. Verificación y Pruebas de Disponibilidad

Una vez levantado el contenedor, ejecutamos los siguientes tests para certificar que todos los compo-nentes del sistema funcionan correctamente:

A. Auditoria de logs del contenedor

Compruebe que Apache ha arrancado sin errores y que no hay excepciones del interprete PHP:

      sudo docker logs enjambre-servidor

La salida no debe contener líneas con [error] de Apache ni advertencias de PHP. Una salida limpia indica que el Dockerfile se ha ejecutado correctamente y que los Virtual Hosts han sido cargados.

B. Test de redireccion HTTP a HTTPS (codigo 301)

      Verificamos que el módulo mod_rewrite está funcionando y que ningún cliente puede acceder al sistema por el canal no cifrado:

      curl -v http://localhost 2>&1 | grep -E 'Location|HTTP/'

La respuesta debe incluir HTTP/1.1 301 Moved Permanently y una cabecera Location: https://localhost/, confirmando que cualquier petición HTTP es elevada automáticamente a HTTPS antes de transmitir datos sensibles.

C. Test de persistencia y conectividad con el SGBD

Este test verifica el punto crítico de la arquitectura: la comunicación entre el contenedor Docker (capa lógica) y MariaDB en el anfitrión (nivel interno) a través del gateway 172.17.0.1. Accedemos a https://[IP_ANFITRION]/index.php e iniciamos sesión con el usuario de prueba:

	- Usuario: alumno_prueba
	- Contraseña: Alumno.Test.2026


El acceso exitoso al dashboard confirma que: el contenedor puede resolver la ruta del nodo de conexión (/var/www/config/db.php), la IP del gateway es alcanzable desde dentro del contenedor, el usuario web_enjambre está autorizado desde la subred 172.17.%.%, y la función password_verify() coteja correctamente el hash BCRYPT almacenado en MariaDB.

D. Verificacion del estado del contenedor

Para confirmar que el contenedor permanece en ejecución tras el lanzamiento:

      sudo docker ps --filter name=enjambre-servidor

La columna STATUS debe mostrar Up seguido del tiempo transcurrido desde el lanzamiento. Si el contenedor aparece como Exited, consulte los logs del paso A para identificar la causa del fallo.



--------------------------------------------------------------------------------
