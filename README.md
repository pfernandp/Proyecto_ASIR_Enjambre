# El despertar del Enjambre

Sistema de Concienciación en Ciberseguridad basado en Escape Room Digital

Este proyecto es una aplicación web dinámica diseñada para introducir y reforzar conceptos básicos de ciberseguridad en el alumnado de Educación Secundaria Obligatoria (ESO). A través de una narrativa de ciencia ficción, los usuarios asumen el rol de operadores del sistema que deben neutralizar a seis Inteligencias Artificiales hostiles, cada una representando un riesgo real del entorno digital.

# Arquitectura e Interfaz Inicial

Actualmente el proyecto se encuentra en su primera fase de entrega (7 de abril), cumpliendo con los siguientes objetivos:

  - Definición de la arquitectura de sistemas (LAMP sobre VirtualBox).
  - Diseño y normalización de la base de datos relacional.
  - Implementación de la identidad visual uniforme y secciones públicas estáticas.

# Stack Tecnológico

El sistema ha sido desplegado siguiendo los estándares de administración de sistemas aprendidos en el ciclo:

  - Virtualización: Oracle VM VirtualBox (aislamiento y portabilidad).
  - SO: Ubuntu 25.10.
  - Servidor Web: Apache2 (puerto TCP 80).
  - Base de Datos: MariaDB con motor InnoDB (garantía de integridad y propiedades ACID).
  - Backend: PHP (intérprete de scripts de servidor para lógica y sesiones).
  - Frontend: HTML5 / CSS3 (estética de terminal de seguridad).

# Replicabilidad y Despliegue

Para replicar este entorno en un servidor Linux compatible, siga estos pasos detallados en el Anexo B de la memoria:

a. Preparación de la Base de Datos
Importe el script consolidado que incluye el DDL (tablas) y el DML (datos iniciales de IAs y usuarios):

- Acceso a MariaDB y creación de la base de datos

      mysql -u root -p -e "CREATE DATABASE enjambre CHARACTER SET utf8 COLLATE utf8_general_ci;"

- Importación del esquema y datos de prueba

      mysql -u root -p enjambre < sql/enjambre.sql

b. Despliegue de la Aplicación Web

Mueva los archivos al DocumentRoot de Apache y configure los permisos necesarios para la ejecución de scripts PHP:

    sudo cp -r www/* /var/www/html/

    sudo chown -R www-data:www-data /var/www/html/

    sudo chmod -R 755 /var/www/html/

# Acceso al Sistema
Identifique la dirección IP de su servidor (ip a) y acceda mediante un navegador web a la ruta inicial: http://[IP_SERVIDOR]/index.php

#Diseño de Datos y Auditoría

La base de datos se fundamenta en un modelo relacional normalizado (3FN) con las siguientes entidades principales:
  -IA: Los seis antagonistas (CLAVE, VELO, ANZUELO, RASTRO, PARÁSITO, NEXO).
  - USUARIO: Gestión de roles (Administrador/Alumno) y credenciales.
  - PRUEBA: Retos técnicos asociados a cada IA.
  - LOG_IA: Tabla de auditoría y trazabilidad para la monitorización de eventos de seguridad.
  - ESTADO_USUARIO_PRUEBA: Registro del progreso y métricas de resolución.

# Estructura del Repositorio

/docs: Memoria técnica detallada en PDF (Proyecto_Pedro_Fernandez.pdf).
/sql: Script autoejecutable enjambre.sql.
/www: Código fuente de la aplicación web (PHP, CSS y recursos gráficos).

--------------------------------------------------------------------------------
Autor: Pedro Fernández Rodríguez - Alumno de ASIR en el IES Suárez de Figueroa de Zafra (Badajoz)
