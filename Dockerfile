FROM php:8.2-apache

# 1. Instalación de extensiones y módulos necesarios
RUN docker-php-ext-install pdo pdo_mysql && a2enmod ssl rewrite

# 2. Inyección de certificados en rutas administrativas de Apache
RUN mkdir -p /etc/apache2/ssl/certificado
COPY ./config/ssl/server.crt /etc/apache2/ssl/certificado/
COPY ./config/ssl/server.key /etc/apache2/ssl/certificado/

# 3. Configuración del sitio seguro por defecto
RUN sed -i 's|/etc/ssl/certs/ssl-cert-snakeoil.pem|/etc/apache2/ssl/certificado/server.crt|g' /etc/apache2/sites-available/default-ssl.conf \
    && sed -i 's|/etc/ssl/private/ssl-cert-snakeoil.key|/etc/apache2/ssl/certificado/server.key|g' /etc/apache2/sites-available/default-ssl.conf \
    && a2ensite default-ssl

# 4. PASO CLAVE: Crear la carpeta config fuera del flujo web (/var/www/config)
# Esto permite que el require_once("../config/db.php") funcione desde /var/www/html/
RUN mkdir -p /var/www/config
COPY ./config/php/db.php /var/www/config/db.php

# 5. Despliegue del código fuente en el DocumentRoot
COPY ./www/ /var/www/html/

# 6. Permisos administrativos (Mínimo privilegio)
RUN chown -R www-data:www-data /var/www/html /var/www/config \
    && chmod -R 755 /var/www/html \
    && chmod 600 /var/www/config/db.php

EXPOSE 80 443
