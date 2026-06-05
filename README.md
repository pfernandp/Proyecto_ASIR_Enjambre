# El Despertar del Enjambre: Escape Room de Ciberseguridad Educativa

"El Despertar del Enjambre" es una aplicación web dinámica diseñada como herramienta de concienciación en ciberseguridad para alumnos de la ESO. 
Bajo una narrativa de ciencia ficción, el usuario actúa como un Operador de Red que debe neutralizar seis Inteligencias Artificiales (IAs) hostiles: 
CLAVE (Contraseñas), VELO (Privacidad), ANZUELO (Phishing), RASTRO (Huella digital), PARÁSITO (Malware) y NEXO (Redes Wi-Fi)

# Arquitectura de Sistemas (Stack LAMP)
El proyecto implementa una Arquitectura de Microservicios Orquestada mediante Docker para garantizar la independencia de servicios y la persistencia de datos.
  
  - Nodo de Aplicación: Servidor Apache 2.4 sobre PHP 8.2 en contenedor inmutable.
  - Nodo de Datos (Nivel Interno): MariaDB 10.11 con motor transaccional InnoDB, garantizando integridad referencial y propiedades ACID.
  - Red Virtual: Red privada enjambre_network que segrega el tráfico interno de la base de datos del acceso público.


# Seguridad y Hardening Aplicado
Como administrador de sistemas, se han implementado medidas de robustecimiento siguiendo estándares OWASP:

  - Capa de Transporte: Forzado de tráfico HTTPS mediante redirección permanente (código 301) en el puerto 80.
  - Criptografía: Hasheo de credenciales con algoritmo BCRYPT (60 caracteres) y certificados RSA de 4096 bits.
  - Defensa Lógica: Mitigación de Inyección SQL mediante el uso sistemático de Sentencias Preparadas (PDO).              
  - Principio de Mínimo Privilegio: Usuario técnico web_enjambre limitado a sentencias DML (SELECT, INSERT, UPDATE).
  - Integridad Referencial: Persistencia en cascada (ON DELETE CASCADE) para la gestión administrativa de borrado de usuarios.

# Protocolo de Despliegue Rápido
Siga exactamente esta secuencia de comandos en una terminal de Ubuntu Server (22.04 LTS o superior) para levantar la infraestructura completa:

1. Preparación del Sistema Anfitrión

Instale el motor de Docker y el orquestador:

    sudo apt update && sudo apt upgrade -y
    sudo apt install -y docker.io docker-compose-v2
    sudo systemctl enable --now docker
      
2. Obtención y Lanzamiento
   
Clone el repositorio y ejecute el despliegue atómico (la construcción inyectará automáticamente el esquema SQL y los activos SSL):

    git clone https://github.com/pfernandp/Proyecto_ASIR_Enjambre.git
    cd Proyecto_ASIR_Enjambre
    sudo docker compose up -d --build
    
3. Auditoría de Salud y Seguridad (Verificación)

Certifique que los servicios y el hardening están operativos:

    - Verificar contenedores activos: sudo docker ps
    - Auditar logs del servidor web: sudo docker logs enjambre_web
    - Test de Redirección 301 (SAD): curl -I http://localhost (Debe devolver HTTP/1.1 301 Moved Permanently hacia HTTPS).

4. Credenciales de Evaluación
Perfiles pre-cargados en el Nivel Interno. Pueden consultarse en el ANEXO A: MANUAL DE USUARIO del proyecto.

--------------------------------------------------------------------------------
Autor: Pedro Fernández Rodríguez – Alumno de fin de ciclo ASIR (IES Suárez de Figueroa).
